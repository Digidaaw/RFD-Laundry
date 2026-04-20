<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PeriodeTransaksiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $transaksis;

    public function __construct($transaksis)
    {
        $this->transaksis = $transaksis;
    }

    public function collection()
    {
        return $this->transaksis;
    }

    public function headings(): array
    {
        return [
            'No Invoice',
            'Tanggal',
            'Pelanggan',
            'Kasir',
            'Total Harga',
            'Jumlah Bayar',
            'Sisa Bayar',
            'Status Pembayaran',
        ];
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->no_invoice,
            optional($transaksi->tanggal_order)->format('d-m-Y'),
            $transaksi->pelanggan->name ?? 'N/A',
            $transaksi->user->name ?? 'N/A',
            $transaksi->total_harga,
            $transaksi->jumlah_bayar,
            $transaksi->sisa_bayar,
            $transaksi->status_pembayaran,
        ];
    }
}
