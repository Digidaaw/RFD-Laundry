<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi - {{ $pelanggan->name }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-red { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Riwayat Transaksi</h2>
        <p>RFD Laundry</p>
    </div>

    <div class="info">
        <strong>Nama Pelanggan:</strong> {{ $pelanggan->name }}<br>
        <strong>Kontak:</strong> {{ $pelanggan->kontak }}<br>
        <strong>Tanggal Cetak:</strong> {{ now()->format('d-m-Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No Invoice</th>
                <th>Tanggal</th>
                <th>Layanan</th>
                <th>Deskripsi</th>
                <th>Total</th>
                <th>Status Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $transaksi)
            <tr>
                <td>{{ $transaksi->no_invoice }}</td>
                <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d-m-Y') }}</td>
                <td>{{ $transaksi->layanan->name ?? '-' }}</td>
                <td>{{ $transaksi->deskripsi ?? '-' }}</td>
                <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                <td>{{ $transaksi->status_pembayaran }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold">Total Transaksi:</td>
                <td colspan="2" class="font-bold">Rp {{ number_format($transaksis->sum('total_harga'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right font-bold">Sisa Hutang:</td>
                <td colspan="2" class="font-bold text-red">Rp {{ number_format($transaksis->sum('sisa_bayar'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>