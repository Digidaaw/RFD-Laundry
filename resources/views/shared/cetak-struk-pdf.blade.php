<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk Transaksi {{ $transaksi->no_invoice }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
        }
        .container {
            width: 100%;
            box-sizing: border-box;
        }
        .header,
        .footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .header strong {
            display: block;
            font-size: 16px;
            margin-bottom: 4px;
        }
        .line {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        .section {
            margin-bottom: 10px;
        }
        .detail-row,
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }
        .detail-row div,
        .total-row div {
            width: 50%;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items th,
        .items td {
            padding: 6px 4px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .items th {
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/rfd laundry.png') }}" alt="Logo RFD Laundry" style="max-width: 200px; max-height: 80px; display: block; margin: 0 auto 10px;">
            <strong>RFD LAUNDRY</strong>
            <div>Jl. Contoh No. 123</div>
            <div>Telp: 08123456789</div>
        </div>

        <div class="line"></div>

        <div class="section detail-row">
            <div>
                <div><strong>No. Invoice:</strong> {{ $transaksi->no_invoice }}</div>
                <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d/m/Y H:i') }}</div>
            </div>
            <div>
                <div><strong>Pelanggan:</strong> {{ $transaksi->pelanggan->name ?? 'N/A' }}</div>
                <div><strong>Kasir:</strong> {{ $transaksi->user->name ?? $transaksi->created_by ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="line"></div>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->items as $item)
                    <tr>
                        <td>{{ $item->layanan->name ?? 'N/A' }} @if($item->unit_satuan) ({{ $item->unit_satuan }}) @endif</td>
                        <td class="text-right">{{ $item->qty }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>

        <div class="section">
            <div class="total-row">
                <div>Subtotal</div>
                <div class="text-right">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</div>
            </div>
            @if($transaksi->potongan > 0)
                <div class="total-row">
                    <div>Potongan</div>
                    <div class="text-right">- Rp {{ number_format($transaksi->potongan, 0, ',', '.') }}</div>
                </div>
            @endif
            <div class="total-row bold">
                <div>Total</div>
                <div class="text-right">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div>Bayar</div>
                <div class="text-right">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</div>
            </div>
            @if($transaksi->sisa_bayar > 0)
                <div class="total-row">
                    <div>Sisa</div>
                    <div class="text-right">Rp {{ number_format($transaksi->sisa_bayar, 0, ',', '.') }}</div>
                </div>
            @else
                <div class="total-row">
                    <div>Kembalian</div>
                    <div class="text-right">Rp {{ number_format(max(0, $transaksi->jumlah_bayar - $transaksi->total_harga), 0, ',', '.') }}</div>
                </div>
            @endif
        </div>

        <div class="line"></div>

        <div class="footer">
            <div>Status: <strong>{{ $transaksi->status_pembayaran }}</strong></div>
            <div>Terima Kasih Atas Kunjungannya</div>
            <div>Barang yang sudah diambil tidak dapat dikembalikan</div>
            <div style="margin-top: 10px;">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>
</body>
</html>
