<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('shared.report.index');
    }

    public function laporanPeriode(Request $request)
    {
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

        // PERBAIKAN: Menambahkan perhitungan baru
        $potensiPendapatan = $transaksis->sum('total_harga'); // Total dari semua invoice
        $pendapatanLunas = $transaksis->sum('jumlah_bayar'); // Total uang yang sudah masuk
        $totalTransaksi = $transaksis->count();

        return view('shared.report.periode', compact('transaksis', 'startDate', 'endDate', 'potensiPendapatan', 'pendapatanLunas', 'totalTransaksi'));
    }

    public function laporanPiutang(Request $request)
    {
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
}
