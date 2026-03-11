<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Writer\Xls; // untuk file .xls
use Carbon\Carbon;


class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi (dengan search)
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'layanan', 'user'])->latest();

        // Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_invoice', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%')
                    ->orWhereHas('pelanggan', function ($pelangganQuery) use ($search) {
                        $pelangganQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $transaksis = $query->get();

        // Data ini diperlukan untuk modal Add dan Edit
        $pelanggans = Pelanggan::orderBy('name')->get();
        $layanans = Layanan::orderBy('name')->get();

        return view('shared.transaksi', [
            'transaksis' => $transaksis,
            'pelanggans' => $pelanggans,
            'layanans' => $layanans,
            'search' => $request->search ?? ''
        ]);
    }

    /**
     * Menyimpan transaksi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'id_layanan' => 'required|exists:layanans,id',
            'berat_laundry' => 'required|numeric|min:0.1',
            'potongan' => 'nullable|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $layanan = Layanan::find($request->id_layanan);
        $subtotal = $layanan->harga * $request->berat_laundry;
        // Potongan bisa datang sebagai null atau string kosong jika pengguna tidak mengisi.
        $potongan = $request->input('potongan');
        if ($potongan === null || $potongan === '') {
            $potongan = 0;
        }
        // pastikan numeric (cast agar tidak ada string tersisa)
        $potongan = (float) $potongan;

        $totalHarga = $subtotal - $potongan;

        if ($totalHarga < 0)
            $totalHarga = 0;

        $sisaBayar = $totalHarga - $request->jumlah_bayar;
        if ($sisaBayar < 0)
            $sisaBayar = 0;

        $transaksi = Transaksi::create([
            'id_user' => Auth::id(),
            'id_pelanggan' => $request->id_pelanggan,
            'id_layanan' => $request->id_layanan,
            'deskripsi' => $request->deskripsi,
            'tanggal_order' => now(),
            'berat_laundry' => $request->berat_laundry,
            'subtotal' => $subtotal,
            'potongan' => $potongan,
            'total_harga' => $totalHarga,
            'jumlah_bayar' => $request->jumlah_bayar,
            'sisa_bayar' => $sisaBayar,
            'status_order' => 'Baru',
            'status_pembayaran' => ($sisaBayar <= 0) ? 'Lunas' : 'Belum Lunas',
        ]);

        $prefix = 'IJ';
        $date = now()->format('dmY');
        $sequence = str_pad($transaksi->id, 4, '0', STR_PAD_LEFT);
        $noInvoice = $prefix . $date . $sequence;

        $transaksi->no_invoice = $noInvoice;
        $transaksi->save();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * PERBAIKAN: Method update sekarang memvalidasi dan menyimpan field baru
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        // Validasi data baru
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'tanggal_order' => 'required|date',
            'deskripsi' => 'nullable|string|max:255',
            'status_order' => 'required|string',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        // Hitung ulang sisa bayar (berdasarkan jumlah bayar baru)
        $sisaBayar = $transaksi->total_harga - $request->jumlah_bayar;
        if ($sisaBayar < 0) {
            $sisaBayar = 0;
        }

        // Simpan data baru ke database
        $transaksi->update([
            'id_pelanggan' => $request->id_pelanggan,
            'tanggal_order' => $request->tanggal_order,
            'deskripsi' => $request->deskripsi,
            'status_order' => $request->status_order,
            'jumlah_bayar' => $request->jumlah_bayar,
            'sisa_bayar' => $sisaBayar,
            'status_pembayaran' => ($sisaBayar <= 0) ? 'Lunas' : 'Belum Lunas',
        ]);

        // Cek jika ada URL redirect khusus (untuk laporan pelanggan)
        if ($request->has('_redirect_url') && $request->_redirect_url != '') {
            // PERBAIKAN: Gunakan redirect()->to()
            return redirect()->to($request->_redirect_url)->with('success', 'Status transaksi berhasil diperbarui.');
        }

        return redirect()->route('transaksi.index')->with('success', 'Status transaksi berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi
     */
    public function destroy(Request $request, Transaksi $transaksi)
    {
        $transaksi->delete();

        // Cek jika ada URL redirect khusus
        if ($request->has('_redirect_url') && $request->_redirect_url != '') {
            // PERBAIKAN: Gunakan redirect()->to()
            return redirect()->to($request->_redirect_url)->with('success', 'Transaksi berhasil dihapus.');
        }

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Membayar piutang
     */
    public function bayarPiutang(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'bayar_sekarang' => 'required|numeric|min:0.01|lte:' . $transaksi->sisa_bayar,
        ], [
            'bayar_sekarang.lte' => 'Jumlah bayar tidak boleh melebihi sisa hutang.',
        ]);

        $jumlahBayarBaru = $transaksi->jumlah_bayar + $request->bayar_sekarang;
        $sisaBayarBaru = $transaksi->total_harga - $jumlahBayarBaru;
        if ($sisaBayarBaru < 0)
            $sisaBayarBaru = 0;

        $transaksi->update([
            'jumlah_bayar' => $jumlahBayarBaru,
            'sisa_bayar' => $sisaBayarBaru,
            'status_pembayaran' => ($sisaBayarBaru <= 0) ? 'Lunas' : 'Belum Lunas',
        ]);

        return redirect()->route('report.piutang')->with('success', 'Pembayaran untuk invoice ' . $transaksi->no_invoice . ' berhasil diterima.');
    }
    public function exportPelangganExcel($pelangganId)
    {
        $data = Transaksi::where('id_pelanggan', $pelangganId)
            ->with('layanan')
            ->latest()
            ->get();

        $fileName = "Laporan-Pelanggan-{$pelangganId}.xls";

        $headers = [
            "Content-Type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$fileName\""
        ];

        $columns = [
            'No Invoice',
            'Tanggal Order',
            'Layanan',
            'Deskripsi',
            'Berat (Kg)',
            'Total Harga',
            'Jumlah Bayar',
            'Sisa Bayar',
            'Status Order',
            'Status Pembayaran'
        ];

        $content = "<table border='1'>
        <thead><tr>";

        foreach ($columns as $col) {
            $content .= "<th><b>$col</b></th>";
        }

        $content .= "</tr></thead><tbody>";

        foreach ($data as $t) {
            $content .= "<tr>
            <td>{$t->no_invoice}</td>
            <td>" . \Carbon\Carbon::parse($t->tanggal_order)->format('d-m-Y') . "</td>
            <td>" . ($t->layanan->name ?? 'N/A') . "</td>
            <td>" . ($t->deskripsi ?? '-') . "</td>
            <td>{$t->berat_laundry}</td>
            <td>{$t->total_harga}</td>
            <td>{$t->jumlah_bayar}</td>
            <td>{$t->sisa_bayar}</td>
            <td>{$t->status_order}</td>
            <td>{$t->status_pembayaran}</td>
        </tr>";
        }

        $content .= "</tbody></table>";

        return response($content, 200, $headers);
    }

}