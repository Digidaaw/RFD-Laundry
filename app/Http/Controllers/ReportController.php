<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPelangganExport; // PENTING: Ini harus ada
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            $columns = [
                'No Invoice',
                'Tanggal',
                'Pelanggan',
                'Kasir',
                'Total',
                'Status Pembayaran',
            ];

            $fileName = 'Laporan-Periode-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xlsx';

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Periode');

            $sheet->fromArray($columns, null, 'A1');
            $sheet->getStyle('A1:F1')->getFont()->setBold(true);

            $row = 2;
            foreach ($transaksis as $t) {
                $sheet->fromArray([
                    $t->no_invoice,
                    \Carbon\Carbon::parse($t->tanggal_order)->format('d-m-Y'),
                    ($t->pelanggan->name ?? 'N/A'),
                    ($t->user->name ?? 'N/A'),
                    $t->total_harga,
                    $t->status_pembayaran,
                ], null, 'A' . $row);
                $row++;
            }

            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

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
            ->with(['layanan', 'items.layanan'])
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
            ->with(['layanan', 'items.layanan'])
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