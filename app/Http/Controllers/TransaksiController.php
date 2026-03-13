<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;


class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi (dengan search)
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'layanan', 'user', 'items.layanan'])->latest();

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
        $isMulti = $request->has('items');

        if ($isMulti) {
            $request->validate([
                'id_pelanggan' => 'required|exists:pelanggans,id',
                'items' => 'required|array|min:1',
                'items.*.id_layanan' => 'required|exists:layanans,id',
                'items.*.berat' => 'required|numeric|min:0.1|max:999.9',
                'potongan' => 'nullable|numeric|min:0',
                'jumlah_bayar' => 'required|numeric|min:0',
                'deskripsi' => 'nullable|string|max:255',
            ]);
        } else {
            // Backward compatible (form lama)
            $request->validate([
                'id_pelanggan' => 'required|exists:pelanggans,id',
                'id_layanan' => 'required|exists:layanans,id',
                'berat_laundry' => 'required|numeric|min:0.1|max:999.9',
                'potongan' => 'nullable|numeric|min:0',
                'jumlah_bayar' => 'required|numeric|min:0',
                'deskripsi' => 'nullable|string|max:255',
            ]);
        }

        // Potongan bisa datang sebagai null / string kosong.
        $potongan = $request->input('potongan');
        if ($potongan === null || $potongan === '') {
            $potongan = 0;
        }
        $potongan = (float) $potongan;

        return DB::transaction(function () use ($request, $isMulti, $potongan) {
            $items = [];

            if ($isMulti) {
                foreach ($request->input('items', []) as $row) {
                    $items[] = [
                        'id_layanan' => (int) ($row['id_layanan'] ?? 0),
                        'berat' => (float) ($row['berat'] ?? 0),
                    ];
                }
            } else {
                $items[] = [
                    'id_layanan' => (int) $request->id_layanan,
                    'berat' => (float) $request->berat_laundry,
                ];
            }

            // Ambil harga layanan sekali (menghindari query per item)
            $layananMap = Layanan::whereIn('id', collect($items)->pluck('id_layanan')->unique()->values())
                ->get()
                ->keyBy('id');

            $subtotalSum = 0.0;
            $beratSum = 0.0;
            $createItems = [];

            foreach ($items as $row) {
                $layanan = $layananMap->get($row['id_layanan']);
                if (!$layanan) {
                    continue;
                }

                $berat = (float) $row['berat'];
                if ($berat <= 0) {
                    continue;
                }

                $hargaSatuan = (float) $layanan->harga;
                $rowSubtotal = $hargaSatuan * $berat;

                $subtotalSum += $rowSubtotal;
                $beratSum += $berat;

                $createItems[] = [
                    'layanan_id' => $layanan->id,
                    'berat' => $berat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $rowSubtotal,
                ];
            }

            if (count($createItems) === 0) {
                return back()
                    ->withInput()
                    ->withErrors(['items' => 'Minimal 1 layanan harus diinput.']);
            }

            // Validasi potongan tidak boleh melebihi subtotal
            if ($potongan > $subtotalSum) {
                return back()
                    ->withInput()
                    ->withErrors(['potongan' => 'Potongan tidak boleh melebihi subtotal.']);
            }

            $jumlahBayar = (float) $request->jumlah_bayar;
            $totalHarga = $subtotalSum - $potongan;
            if ($totalHarga < 0) {
                $totalHarga = 0;
            }

            $sisaBayar = max(0, $totalHarga - $jumlahBayar);

            // Use the first layanan id as a representative value for the transaction.
            // This avoids SQL errors when the database still requires a non-null id_layanan
            // while the transaction contains multiple layanan items.
            $singleLayananId = $createItems[0]['layanan_id'] ?? null;

            $transaksi = Transaksi::create([
                'id_user' => Auth::id(),
                'created_by' => Auth::user()->username,
                'id_pelanggan' => $request->id_pelanggan,
                'id_layanan' => $singleLayananId,
                'deskripsi' => $request->deskripsi,
                'tanggal_order' => now(),
                'berat_laundry' => $beratSum,
                'subtotal' => $subtotalSum,
                'potongan' => $potongan,
                'total_harga' => $totalHarga,
                'jumlah_bayar' => $jumlahBayar,
                'sisa_bayar' => $sisaBayar,
                'status_order' => 'Baru',
                'status_pembayaran' => ($sisaBayar <= 0) ? 'Lunas' : 'Belum Lunas',
            ]);

            $transaksi->items()->createMany($createItems);

            $prefix = 'IJ';
            $date = now()->format('dmY');
            $sequence = str_pad($transaksi->id, 4, '0', STR_PAD_LEFT);
            $noInvoice = $prefix . $date . $sequence;

            $transaksi->no_invoice = $noInvoice;
            $transaksi->save();

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
        });
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
        if ($request->jumlah_bayar > $transaksi->total_harga) {
            return back()
                ->withInput()
                ->withErrors(['jumlah_bayar' => 'Jumlah bayar tidak boleh melebihi total tagihan.']);
        }

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

        $fileName = "Laporan-Pelanggan-{$pelangganId}.xlsx";

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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan');

        // Header
        $sheet->fromArray($columns, null, 'A1');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $row = 2;
        foreach ($data as $t) {
            $sheet->fromArray([
                $t->no_invoice,
                \Carbon\Carbon::parse($t->tanggal_order)->format('d-m-Y'),
                ($t->layanan->name ?? 'N/A'),
                ($t->deskripsi ?? '-'),
                $t->berat_laundry,
                $t->total_harga,
                $t->jumlah_bayar,
                $t->sisa_bayar,
                $t->status_order,
                $t->status_pembayaran,
            ], null, 'A' . $row);
            $row++;
        }

        // Auto width sederhana
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

}