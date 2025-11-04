<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
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
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'id_layanan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
