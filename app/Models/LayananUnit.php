<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananUnit extends Model
{
    use HasFactory;
    protected $fillable = [
        'layanan_id',
        'unit_satuan',
        'harga',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}
