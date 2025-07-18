<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['pelanggan', 'layanan', 'user'])->latest()->get();
        $pelanggans = Pelanggan::orderBy('name')->get();
        $layanans = Layanan::orderBy('name')->get();
        return view('shared.transaksi', compact('transaksis', 'pelanggans', 'layanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id',
            'id_layanan' => 'required|exists:layanans,id',
            'berat_laundry' => 'required|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        $layanan = Layanan::find($request->id_layanan);
        $totalHarga = $layanan->harga * $request->berat_laundry;

        // PERBAIKAN: Hitung sisa bayar dan pastikan tidak negatif
        $sisaBayar = $totalHarga - $request->jumlah_bayar;
        if ($sisaBayar < 0) {
            $sisaBayar = 0; // Jika ada kembalian, sisa bayar dianggap 0
        }

        $transaksi = Transaksi::create([
            'id_user' => Auth::id(),
            'id_pelanggan' => $request->id_pelanggan,
            'id_layanan' => $request->id_layanan,
            'tanggal_order' => now(),
            'berat_laundry' => $request->berat_laundry,
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

    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'status_order' => 'required|string',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        // PERBAIKAN: Hitung sisa bayar dan pastikan tidak negatif
        $sisaBayar = $transaksi->total_harga - $request->jumlah_bayar;
        if ($sisaBayar < 0) {
            $sisaBayar = 0;
        }

        $transaksi->update([
            'status_order' => $request->status_order,
            'jumlah_bayar' => $request->jumlah_bayar,
            'sisa_bayar' => $sisaBayar,
            'status_pembayaran' => ($sisaBayar <= 0) ? 'Lunas' : 'Belum Lunas',
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Status transaksi berhasil diperbarui.');
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }


    /**
     * PERUBAHAN: Method baru untuk menangani pembayaran piutang.
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
        if ($sisaBayarBaru < 0) {
            $sisaBayarBaru = 0;
        }

        $transaksi->update([
            'jumlah_bayar' => $jumlahBayarBaru,
            'sisa_bayar' => $sisaBayarBaru,
            'status_pembayaran' => ($sisaBayarBaru <= 0) ? 'Lunas' : 'Belum Lunas',
        ]);

        return redirect()->route('report.piutang')->with('success', 'Pembayaran untuk invoice ' . $transaksi->no_invoice . ' berhasil diterima.');
    }
}
