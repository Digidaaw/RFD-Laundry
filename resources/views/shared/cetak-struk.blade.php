@extends('layout.admin.layout')
@section('title', 'Cetak Struk - ' . $transaksi->no_invoice)

@section('content')
<style>
@media print {
    @page {
        size: 80mm auto;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.2;
        width: 80mm;
    }
    .struk-container {
        width: 100%;
        max-width: 80mm;
        padding: 5mm;
        box-sizing: border-box;
    }
    .center {
        text-align: center;
    }
    .right {
        text-align: right;
    }
    .bold {
        font-weight: bold;
    }
    .line {
        border-bottom: 1px dashed #000;
        margin: 5px 0;
    }
    .no-print {
        display: none !important;
    }
}

.struk-container {
    width: 80mm;
    margin: 0 auto;
    padding: 5mm;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    line-height: 1.2;
    background: white;
    border: 1px solid #ccc;
}

.center {
    text-align: center;
}

.right {
    text-align: right;
}

.bold {
    font-weight: bold;
}

.line {
    border-bottom: 1px dashed #000;
    margin: 5px 0;
}

.no-print {
    margin-top: 20px;
    text-align: center;
}
</style>

<div class="struk-container">
    <div class="center bold">
        RFD LAUNDRY<br>
        Jl. Contoh No. 123<br>
        Telp: 08123456789<br>
    </div>

    <div class="line"></div>

    <div>
        <strong>No. Invoice:</strong> {{ $transaksi->no_invoice }}<br>
        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d/m/Y H:i') }}<br>
        <strong>Pelanggan:</strong> {{ $transaksi->pelanggan->name ?? 'N/A' }}<br>
        <strong>Kasir:</strong> {{ $transaksi->created_by ?? 'N/A' }}<br>
    </div>

    <div class="line"></div>

    <div class="bold center">DETAIL LAYANAN</div>

    @foreach($transaksi->items as $item)
    <div>
        {{ $item->layanan->name ?? 'N/A' }}<br>
        {{ $item->qty }} {{ $item->unit_satuan ?? 'pcs' }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}<br>
        <div class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
    </div>
    @endforeach

    <div class="line"></div>

    <div>
        <div>Subtotal: <span class="right">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span></div>
        @if($transaksi->potongan > 0)
        <div>Potongan: <span class="right">- Rp {{ number_format($transaksi->potongan, 0, ',', '.') }}</span></div>
        @endif
        <div class="bold">Total: <span class="right">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span></div>
        <div>Bayar: <span class="right">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span></div>
        @if($transaksi->sisa_bayar > 0)
        <div>Sisa: <span class="right">Rp {{ number_format($transaksi->sisa_bayar, 0, ',', '.') }}</span></div>
        @else
        <div>Kembalian: <span class="right">Rp {{ number_format($transaksi->jumlah_bayar - $transaksi->total_harga, 0, ',', '.') }}</span></div>
        @endif
    </div>

    <div class="line"></div>

    <div class="center">
        Status: <strong>{{ $transaksi->status_pembayaran }}</strong><br>
        Terima Kasih Atas Kunjungannya<br>
        Barang yang sudah diambil tidak dapat dikembalikan
    </div>

    <div class="line"></div>

    <div class="center">
        Dicetak: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</div>

<div class="no-print">
    <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Cetak Struk
    </button>
    <a href="{{ route('transaksi.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 ml-2">
        Kembali
    </a>
</div>

<script>
window.onload = function() {
    // Auto print jika diakses langsung untuk cetak
    if (window.location.search.includes('print=1')) {
        window.print();
    }
}
</script>
@endsection