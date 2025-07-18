<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'gambar',
        'harga',
        'deskripsi',
    ];

    protected $casts = [
        // PERUBAHAN: Memberitahu Laravel bahwa kolom 'gambar' adalah array
        'gambar' => 'array',
    ];
}
