<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan; // <-- Tambahkan ini
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ... (method index, laporanPeriode, laporanPiutang Anda yang sudah ada) ...
    public function index()
    {
        return view('shared.report.index');
    }

    public function laporanPeriode(Request $request)
    {
        // ... (kode Anda)
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $transaksis = Transaksi::with(['pelanggan', 'user'])
            ->whereBetween('tanggal_order', [$startDate, $endDate])
            ->latest()
            ->get();

        $potensiPendapatan = $transaksis->sum('total_harga');
        $pendapatanLunas = $transaksis->sum('jumlah_bayar');
        $totalTransaksi = $transaksis->count();

        return view('shared.report.periode', compact('transaksis', 'startDate', 'endDate', 'potensiPendapatan', 'pendapatanLunas', 'totalTransaksi'));
    }

    public function laporanPiutang(Request $request)
    {
        // ... (kode Anda)
        $search = $request->input('search');

        $query = Transaksi::with(['pelanggan'])
            ->where('status_pembayaran', 'Belum Lunas')
            ->where('sisa_bayar', '>', 0);

        if ($search) {
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kontak', 'like', "%{$search}%");
            });
        }

        $piutangs = $query->latest()->get();
        $totalPiutang = $piutangs->sum('sisa_bayar');

        return view('shared.report.piutang', compact('piutangs', 'totalPiutang'));
    }
    

    /**
     * FITUR BARU: Menampilkan laporan detail untuk satu pelanggan
     */
    public function laporanPelanggan(Pelanggan $pelanggan)
    {
        // 1. Ambil semua transaksi milik pelanggan ini
        $transaksis = Transaksi::where('id_pelanggan', $pelanggan->id)
            ->with('layanan')
            ->latest()
            ->get();
            
        // 2. PERBAIKAN: Ambil juga daftar semua pelanggan untuk modal edit
        $pelanggans = Pelanggan::orderBy('name')->get();

        // 3. Hitung totalan akhir
        $totalSubtotal = $transaksis->sum('subtotal');
        // ... (perhitungan lainnya)
        $totalPotongan = $transaksis->sum('potongan');
        $totalHarga = $transaksis->sum('total_harga');
        $totalSudahBayar = $transaksis->sum('jumlah_bayar');
        $totalSisaHutang = $transaksis->sum('sisa_bayar');

        // 4. Kirim semua data ke view
        return view('shared.report.pelanggan', compact(
            'pelanggan',
            'transaksis',
            'pelanggans', // <-- Kirimkan variabel ini
            'totalSubtotal',
            'totalPotongan',
            'totalHarga',
            'totalSudahBayar',
            'totalSisaHutang'
        ));
    }
}