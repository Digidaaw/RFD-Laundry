<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaksi->no_invoice }}</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 10mm 10mm 10mm;
        }

        body{
            margin:0;
            padding:0;
            font-family: "Times New Roman", serif;
            color:#000;
            font-size:15px;
        }

        .wrapper{
            width:100%;
        }

        /* =========================
           HEADER
        ==========================*/
        .header{
            text-align:center;
            margin-bottom:8px;
        }

        .header img{
            width:180px;
            margin-bottom:2px;
        }

        .company-desc{
            font-family: Arial, sans-serif;
            font-size:15px;
            line-height:1.1;
            font-weight:bold;
        }

        .line{
            border-top:2px solid #000;
            margin-top:5px;
            margin-bottom:5px;
        }

        /* =========================
           TOP INFO
        ==========================*/
        .top-info{
            width:100%;
            border-collapse:collapse;
            margin-bottom:5px;
        }

        .top-info td{
            padding:1px 3px;
            font-size:15px;
        }

        .label{
            width:130px;
        }

        .colon{
            width:10px;
        }

        .right-info{
            text-align:left;
            padding-left:100px;
        }

        .line-bottom{
            border-bottom:2px solid #000;
            margin-bottom:3px;
        }

        /* =========================
           TABLE ITEM
        ==========================*/
        .items{
            width:100%;
            border-collapse:collapse;
            font-size:15px;
        }

        .items thead th{
            text-align:left;
            padding:2px 3px;
            font-weight:normal;
            border-bottom:1px solid #000;
            font-size:15px;
        }

        .items tbody td{
            padding:1px 2px;
            vertical-align:top;
        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        /* =========================
           SUMMARY
        ==========================*/
        .summary{
            width:48%;
            margin-left:auto;
            margin-top:3px;
            border-collapse:collapse;
            font-size:15px;
        }

        .summary td{
            padding:1px 3px;
        }

        .summary .label{
            width:150px;
        }

        .summary .value{
            text-align:right;
            width:180px;
        }

        /* =========================
           NOTE
        ==========================*/
        .note{
            margin-top:3px;
            font-size:15px;
            line-height:1.2;
        }

        /* =========================
           SIGNATURE
        ==========================*/
        .signature{
            width:100%;
            margin-top:15px;
            border-collapse:collapse;
        }

        .signature td{
            width:50%;
            vertical-align:top;
            font-size:15px;
        }

        .signature .right{
            text-align:center;
        }

        .signature .left{
            text-align:left;
        }

        .sign-space{
            height:40px;
        }

        /* =========================
           FOOTER
        ==========================*/
        .footer{
            width:100%;
            margin-top:5px;
            text-align:right;
            font-style:italic;
            font-size:15px;
        }

    </style>
</head>
<body>

<div class="wrapper">

    <!-- HEADER -->
    <div class="header">
        @if (extension_loaded('gd'))
            <img src="{{ public_path('images/rfd laundry.png') }}" alt="logo">
        @endif

        <div class="company-desc">
            Garment wash,bleach wash,Stone wash,sand wash<br>
            Bio wash, enzyme wash
        </div>
    </div>

    <div class="line"></div>

    <!-- TOP INFO -->
    <table class="top-info">
        <tr>
            <td class="label">Invoice no.</td>
            <td class="colon">:</td>
            <td>{{ $transaksi->no_invoice }}</td>

            <td class="right-info">To</td>
            <td class="colon">:</td>
            <td>
                {{ $transaksi->pelanggan->name ?? 'Fabrizio' }}
            </td>
        </tr>

        <tr>
            <td class="label">Date</td>
            <td class="colon">:</td>
            <td>
                {{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('Y-m-d') }}
            </td>

            <td class="right-info">Status</td>
            <td class="colon">:</td>
            <td>
                {{ $transaksi->status_pembayaran ?? '-' }}
            </td>
        </tr>
    </table>

    <div class="line-bottom"></div>

    <!-- TABLE -->
    <table class="items">
        <thead>
        <tr>
            <th width="12%">Date</th>
            <th width="30%">Type of Wash</th>
            <th width="16%">Description</th>
            <th width="9%">Satuan</th>
            <th width="10%">Jumlah</th>
            <th width="11%">Harga</th>
            <th width="12%" class="text-right">Subtotal</th>
        </tr>
        </thead>

        <tbody>
        @php
            $totalQty = 0;
        @endphp

        @foreach($transaksi->items as $item)

            @php
                $totalQty += $item->qty;
            @endphp

            <tr>
                <td>
                    {{ \Carbon\Carbon::parse($transaksi->tanggal_order)->format('d-m-Y') }}
                </td>

                <td>
                    {{ $item->layanan->name ?? '-' }}
                </td>

                <td>
                    {{ $item->transaksi->deskripsi ?? '-' }}
                </td>

                <td>
                    {{ $item->unit_satuan ?? '-' }}
                </td>

                <td>
                    {{ number_format($item->qty, 2, ',', '.') }}
                </td>

                <td>
                    Rp {{ number_format($item->harga_satuan, 2, ',', '.') }}
                </td>

                <td class="text-right">
                    Rp {{ number_format($item->subtotal, 2, ',', '.') }}
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>

    <!-- SUMMARY -->
    <table class="summary">
        <tr>
            <td class="label">Sub Total</td>
            <td class="value">
                Rp {{ number_format($transaksi->subtotal, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td class="label">Potongan</td>
            <td class="value">
                Rp {{ number_format($transaksi->potongan ?? 0, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td class="label">Total</td>
            <td class="value">
                Rp {{ number_format($transaksi->total_harga, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td class="label">Jumlah Bayar</td>
            <td class="value">
                Rp {{ number_format($transaksi->jumlah_bayar ?? 0, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td class="label">Kembalian</td>
            <td class="value">
                Rp {{ number_format($transaksi->kembalian ?? 0, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <td class="label">Sisa Bayar</td>
            <td class="value">
                Rp {{ number_format($transaksi->sisa_bayar ?? 0, 2, ',', '.') }}
            </td>
        </tr>
    </table>

    <!-- NOTE -->
    <div class="note">
        Note :
        {{ $transaksi->deskripsi ?? '' }}
    </div>

    <!-- SIGN -->
    <table class="signature">
        <tr>
            <td class="left">
                Tanda Terima

                <div class="sign-space"></div>

                {{ $transaksi->pelanggan->name ?? 'Fabrizio' }}
            </td>

            <td class="right">
                Hormat Kami

                <div class="sign-space"></div>

                {{ $transaksi->user->name ?? 'tanto' }}
            </td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        Halaman 1 dari 1
    </div>

</div>

</body>
</html>
