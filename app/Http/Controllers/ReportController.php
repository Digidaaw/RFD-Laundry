<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPelangganExport;
use App\Exports\PeriodeTransaksiExport;
use App\Http\Requests\ReportPeriodeRequest;
use App\Http\Requests\ReportPiutangRequest;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('shared.report.index');
    }

    public function laporanPeriode(ReportPeriodeRequest $request)
    {
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

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('shared.report.periode_pdf', compact(
                'transaksis',
                'startDate',
                'endDate',
                'potensiPendapatan',
                'pendapatanLunas',
                'totalTransaksi'
            ));

            return $pdf->download('Laporan-Periode-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf');
        }

        if ($request->get('export') === 'excel') {
            $fileName = 'Laporan-Periode-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xlsx';

            return Excel::download(new PeriodeTransaksiExport($transaksis), $fileName);
        }

        return view('shared.report.periode', compact('transaksis', 'startDate', 'endDate', 'potensiPendapatan', 'pendapatanLunas', 'totalTransaksi'));
    }

    public function laporanPiutang(ReportPiutangRequest $request)
    {
        $search = $request->input('search');
        $piutangs = $this->buildPiutangQuery($search)->latest()->get();
        $totalPiutang = $piutangs->sum('sisa_bayar');
        $pelanggans = Pelanggan::orderBy('name')->get();
        $layanans = Layanan::with('units')->orderBy('name')->get();

        return view('shared.report.piutang', compact('piutangs', 'totalPiutang', 'pelanggans', 'layanans'));
    }

    public function laporanPelanggan(Request $request, Pelanggan $pelanggan)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate   = $request->filled('end_date')   ? Carbon::parse($request->input('end_date'))->endOfDay()   : Carbon::now()->endOfDay();

        $transaksis = $this->getPelangganTransaksis($pelanggan, $startDate, $endDate);
        $pelanggans = Pelanggan::orderBy('name')->get();
        $layanans   = Layanan::with('units')->orderBy('name')->get();

        $totalSubtotal  = $transaksis->sum('subtotal');
        $totalPotongan  = $transaksis->sum('potongan');
        $totalHarga     = $transaksis->sum('total_harga');
        $totalSudahBayar = $transaksis->sum('jumlah_bayar');
        $totalSisaHutang = $transaksis->sum('sisa_bayar');

        return view('shared.report.pelanggan', compact(
            'pelanggan',
            'transaksis',
            'pelanggans',
            'layanans',
            'totalSubtotal',
            'totalPotongan',
            'totalHarga',
            'totalSudahBayar',
            'totalSisaHutang',
            'startDate',
            'endDate'
        ));
    }

    // --- FUNGSI EXPORT ---

    public function exportPdfPelanggan(Request $request, Pelanggan $pelanggan)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate   = $request->filled('end_date')   ? Carbon::parse($request->input('end_date'))->endOfDay()   : Carbon::now()->endOfDay();

        $transaksis = $this->getPelangganTransaksis($pelanggan, $startDate, $endDate);

        $pdf = Pdf::loadView('shared.report.pdf_pelanggan', compact('pelanggan', 'transaksis', 'startDate', 'endDate'));

        $suffix = ($startDate && $endDate)
            ? '-' . $startDate->format('Ymd') . '-sd-' . $endDate->format('Ymd')
            : '';

        return $pdf->download('Laporan-Pelanggan-' . $pelanggan->name . $suffix . '.pdf');
    }

    public function exportPelangganXls(Request $request, Pelanggan $pelanggan)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate   = $request->filled('end_date')   ? Carbon::parse($request->input('end_date'))->endOfDay()   : Carbon::now()->endOfDay();

        $suffix = ($startDate && $endDate)
            ? '-' . $startDate->format('Ymd') . '-sd-' . $endDate->format('Ymd')
            : '';

        return Excel::download(
            new LaporanPelangganExport($pelanggan->id, $startDate, $endDate),
            'Laporan-Pelanggan-' . $pelanggan->name . $suffix . '.xlsx'
        );
    }

    private function buildPiutangQuery(?string $search)
    {
        $query = Transaksi::with(['pelanggan'])
            ->where('status_pembayaran', 'DP')
            ->where('sisa_bayar', '>', 0);

        if ($search) {
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function getPelangganTransaksis(Pelanggan $pelanggan, $startDate = null, $endDate = null)
    {
        $query = Transaksi::where('id_pelanggan', $pelanggan->id)
            ->with(['layanan', 'items.layanan']);

        if ($startDate) {
            $query->where('tanggal_order', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tanggal_order', '<=', $endDate);
        }

        return $query->latest()->get();
    }
}
