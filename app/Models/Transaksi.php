<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_pelanggan',
        'id_layanan',
        'tanggal_order',
        'berat_laundry',
        'total_harga',
        'jumlah_bayar',
        'sisa_bayar',
        'status_order',
        'status_pembayaran'
    ];

}
