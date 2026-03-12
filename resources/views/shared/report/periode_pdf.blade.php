<!DOCTYPE html>
<html>
<head>
    <title>Laporan Periode {{ $startDate->format('d-m-Y') }} s/d {{ $endDate->format('d-m-Y') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin-top: 12px; }
        .summary td { border: none; padding: 3px 0; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Transaksi Periode</h2>
        <p>RFD Laundry</p>
        <p>Periode: {{ $startDate->format('d-m-Y') }} s/d {{ $endDate->format('d-m-Y') }}</p>
        <p>Tanggal Cetak: {{ now()->format('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th>Total</th>
                <th>Status Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $t)
                <tr>
                    <td>{{ $t->no_invoice }}</td>
                    <td>{{ \Carbon\Carbon::parse($t->tanggal_order)->format('d-m-Y') }}</td>
                    <td>{{ $t->pelanggan->name ?? 'N/A' }}</td>
                    <td>{{ $t->user->name ?? 'N/A' }}</td>
                    <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $t->status_pembayaran }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td class="font-bold">Potensi Pendapatan:</td>
            <td class="text-right">Rp {{ number_format($potensiPendapatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="font-bold">Pendapatan Diterima:</td>
            <td class="text-right">Rp {{ number_format($pendapatanLunas, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="font-bold">Jumlah Transaksi:</td>
            <td class="text-right">{{ $totalTransaksi }}</td>
        </tr>
    </table>
</body>
</html>

