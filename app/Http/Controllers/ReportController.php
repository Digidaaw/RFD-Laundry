<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPelangganExport; // PENTING: Ini harus ada

class ReportController extends Controller
{
    public function index()
    {
        return view('shared.report.index');
    }

    public function laporanPeriode(Request $request)
    {
        // ... (kode laporan periode tetap sama) ...
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
        // ... (kode laporan piutang tetap sama) ...
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

    public function laporanPelanggan(Pelanggan $pelanggan)
    {
        $transaksis = Transaksi::where('id_pelanggan', $pelanggan->id)
            ->with('layanan')
            ->latest()
            ->get();

        $pelanggans = Pelanggan::orderBy('name')->get();

        $totalSubtotal = $transaksis->sum('subtotal');
        $totalPotongan = $transaksis->sum('potongan');
        $totalHarga = $transaksis->sum('total_harga');
        $totalSudahBayar = $transaksis->sum('jumlah_bayar');
        $totalSisaHutang = $transaksis->sum('sisa_bayar');

        return view('shared.report.pelanggan', compact(
            'pelanggan',
            'transaksis',
            'pelanggans',
            'totalSubtotal',
            'totalPotongan',
            'totalHarga',
            'totalSudahBayar',
            'totalSisaHutang'
        ));
    }

    // --- FUNGSI EXPORT ---

    public function exportPdfPelanggan(Pelanggan $pelanggan)
    {
        $transaksis = Transaksi::where('id_pelanggan', $pelanggan->id)
            ->with('layanan')
            ->latest()
            ->get();

        $pdf = Pdf::loadView('shared.report.pdf_pelanggan', compact('pelanggan', 'transaksis'));

        return $pdf->download('Laporan-Pelanggan-' . $pelanggan->name . '.pdf');
    }

    public function exportPelangganXls($id)
    {
        return (new LaporanPelangganExport($id))->download();
    }
}