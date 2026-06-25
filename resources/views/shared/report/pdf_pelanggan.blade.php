<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi - {{ $pelanggan->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #111; }
        h2   { text-align: center; margin: 0 0 4px; font-size: 16px; }
        .sub { text-align: center; margin: 0 0 16px; font-size: 11px; color: #555; }

        .info { margin-bottom: 14px; line-height: 1.7; }
        .info strong { display: inline-block; width: 110px; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }

        /* Header tabel */
        thead th {
            background-color: #2d3748;
            color: #fff;
            padding: 7px 6px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        thead th.text-right  { text-align: right; }
        thead th.text-center { text-align: center; }

        /* Body tabel */
        tbody td {
            padding: 5px 4px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 10px;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        tbody tr:nth-child(even) { background-color: #f7fafc; }

        /* Item sub-row (baris layanan) */
        .item-row td { padding: 3px 4px; border-bottom: none; font-size: 10px; }
        .item-row td.indent { padding-left: 12px; }

        /* Footer tabel */
        tfoot td {
            padding: 5px 4px;
            font-weight: bold;
            font-size: 10px;
            word-wrap: break-word;
        }
        tfoot .label { text-align: right; }
        tfoot .value { text-align: right; }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .text-red    { color: #c0392b; }
        .text-green  { color: #27ae60; }
        .badge-lunas { color: #27ae60; font-weight: bold; }
        .badge-dp    { color: #e67e22; font-weight: bold; }
        .divider     { border-top: 2px solid #2d3748; margin: 16px 0 8px; }

        /* Colspan summary section */
        .summary-section { margin-top: 12px; width: 40%; margin-left: auto; }
        .summary-section table { margin-top: 0; }
        .summary-section td { padding: 4px 6px; border: none; font-size: 11px; }
    </style>
</head>
<body>

    <h2>Laporan Riwayat Transaksi</h2>
    <p class="sub">RFD Laundry</p>

    <div class="info">
        <strong>Nama Pelanggan:</strong> {{ $pelanggan->name }}<br>
        <strong>Kontak:</strong> {{ $pelanggan->kontak }}<br>
        @if($startDate && $endDate)
            <strong>Periode:</strong> {{ $startDate->format('d-m-Y') }} s.d. {{ $endDate->format('d-m-Y') }}<br>
        @else
            <strong>Periode:</strong> Semua Riwayat Transaksi<br>
        @endif
        <strong>Tanggal Cetak:</strong> {{ now()->format('d-m-Y H:i') }}
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="width:14%">No Invoice</th>
                <th style="width:9%">Tanggal</th>
                <th style="width:14%">Layanan</th>
                <th class="text-center" style="width:6%">Satuan</th>
                <th class="text-center" style="width:6%">Qty</th>
                <th class="text-right" style="width:12%">Harga/Satuan</th>
                <th class="text-right" style="width:10%">Subtotal</th>
                <th style="width:19%">Deskripsi</th>
                <th class="text-right" style="width:10%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $transaksi)
                @php
                    $items = $transaksi->items;
                    $itemCount = $items->count();
                    $rowspan = max($itemCount, 1);
                @endphp

                @if($itemCount > 0)
                    @foreach($items as $loopIndex => $item)
                        <tr>
                            @if($loopIndex === 0)
                                {{-- Kolom invoice, tanggal, deskripsi, total hanya di baris pertama --}}
                                <td rowspan="{{ $rowspan }}" style="vertical-align:top; font-weight:bold;">
                                    {{ $transaksi->no_invoice }}
                                </td>
                                <td rowspan="{{ $rowspan }}" style="vertical-align:top;">
                                    {{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d-m-Y') }}
                                </td>
                            @endif

                            {{-- Kolom layanan & detail per item --}}
                            <td>{{ $item->layanan->name ?? 'N/A' }}</td>
                            <td class="text-center">{{ $item->unit_satuan ?? '-' }}</td>
                            <td class="text-center">
                                @if($item->unit_satuan === 'pcs')
                                    {{ number_format($item->qty, 0, ',', '.') }}
                                @else
                                    {{ rtrim(rtrim(number_format($item->qty, 2, ',', '.'), '0'), ',') }}
                                @endif
                            </td>
                            <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>

                            @if($loopIndex === 0)
                                <td rowspan="{{ $rowspan }}" style="vertical-align:top; color:#555;">
                                    {{ $transaksi->deskripsi ?? '-' }}
                                </td>
                                <td rowspan="{{ $rowspan }}" class="text-right" style="vertical-align:top; font-weight:bold;">
                                    Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="font-weight:bold;">{{ $transaksi->no_invoice }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d-m-Y') }}</td>
                        <td>{{ $transaksi->layanan->name ?? '-' }}</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td style="color:#555;">{{ $transaksi->deskripsi ?? '-' }}</td>
                        <td class="text-right" style="font-weight:bold;">
                            Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="label">Total Transaksi:</td>
                <td class="value">Rp {{ number_format($transaksis->sum('total_harga'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="8" class="label">Total Sudah Dibayar:</td>
                <td class="value text-green">Rp {{ number_format($transaksis->sum('jumlah_bayar'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="8" class="label">Sisa Hutang:</td>
                <td class="value text-red">Rp {{ number_format($transaksis->sum('sisa_bayar'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>