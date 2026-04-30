<?php

namespace App\Exports;

use App\Models\Transaksi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanPelangganExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $pelangganId;

    public function __construct($pelangganId)
    {
        $this->pelangganId = $pelangganId;
    }

    public function collection()
    {
        return Transaksi::where('id_pelanggan', $this->pelangganId)
            ->with(['layanan', 'items.layanan'])
            ->latest()
            ->get();
    }

    public function headings(): array
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
            'Status Pembayaran',
        ];
    }

    public function map($transaksi): array
    {
        $layananText = $transaksi->items->count()
            ? $transaksi->items->map(fn($item) => ($item->layanan->name ?? 'N/A') . ' (' . $item->qty . ')')->join(', ')
            : ($transaksi->layanan->name ?? 'N/A');

        return [
            $transaksi->no_invoice,
            Carbon::parse($transaksi->tanggal_order)->format('d-m-Y'),
            $layananText,
            $transaksi->deskripsi ?? '-',
            $transaksi->berat_laundry,
            $transaksi->total_harga,
            $transaksi->jumlah_bayar,
            $transaksi->sisa_bayar,
            $transaksi->status_pembayaran,
        ];
    }
}
