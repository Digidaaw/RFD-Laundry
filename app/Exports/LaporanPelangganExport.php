<?php

namespace App\Exports;

use App\Models\Transaksi;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPelangganExport
{
    protected $pelangganId;

    public function __construct($pelangganId)
    {
        $this->pelangganId = $pelangganId;
    }

    // Ambil data transaksi
    public function collection()
    {
        return Transaksi::where('id_pelanggan', $this->pelangganId)
            ->with('layanan')
            ->latest()
            ->get();
    }

    // Header kolom
    public function headings()
    {
        return [
            'No Invoice',
            'Tanggal Order',
            'Layanan',
            'Deskripsi',
            'Berat (Kg)',
            'Total Harga',
            'Jumlah Bayar',
            'Sisa Bayar',
            'Status Order',
            'Status Pembayaran',
        ];
    }

    // Mapping data per baris
    public function map($transaksi)
    {
        return [
            $transaksi->no_invoice,
            Carbon::parse($transaksi->tanggal_order)->format('d-m-Y'),
            $transaksi->layanan->name ?? 'N/A',
            $transaksi->deskripsi ?? '-',
            $transaksi->berat_laundry,
            $transaksi->total_harga,
            $transaksi->jumlah_bayar,
            $transaksi->sisa_bayar,
            $transaksi->status_order,
            $transaksi->status_pembayaran,
        ];
    }

    // Fungsi export untuk XLS
    public function download()
    {
        $data = $this->collection();

        return Excel::create('Laporan_Pelanggan', function($excel) use ($data) {
            $excel->sheet('Laporan', function($sheet) use ($data) {

                // Set Header
                $sheet->row(1, $this->headings());
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                });

                $row = 2;

                foreach ($data as $item) {
                    $sheet->row($row, $this->map($item));
                    $row++;
                }

                // Auto size
                foreach (range('A', 'J') as $col) {
                    $sheet->setWidth($col, 20);
                }
            });
        })->export('xls'); // XLS (tidak perlu ZIP)
    }
}
