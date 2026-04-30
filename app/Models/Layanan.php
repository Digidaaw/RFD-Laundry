<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'gambar',
        'deskripsi',
    ];

    protected $casts = [
        // PERUBAHAN: Memberitahu Laravel bahwa kolom 'gambar' adalah array
        'gambar' => 'array',
    ];

    public function units()
    {
    return $this->hasMany(LayananUnit::class);
    }
}
