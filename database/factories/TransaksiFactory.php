<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiFactory extends Factory
{
    public function definition(): array
    {
        $subtotal   = fake()->numberBetween(10000, 200000);
        $potongan   = 0;
        $total      = $subtotal - $potongan;
        $bayar      = $total;
        $sisa       = 0;

        return [
            'no_invoice'        => 'IJ' . now()->format('dmY') . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'id_user'           => User::factory(),
            'created_by'        => 'admin',
            'id_pelanggan'      => Pelanggan::factory(),
            'id_layanan'        => null,
            'tanggal_order'     => now(),
            'deskripsi'         => null,
            'subtotal'          => $subtotal,
            'potongan'          => $potongan,
            'total_harga'       => $total,
            'jumlah_bayar'      => $bayar,
            'sisa_bayar'        => $sisa,
            'status_pembayaran' => 'Lunas',
        ];
    }

    public function dp(): static
    {
        return $this->state(function (array $attributes) {
            $total = $attributes['total_harga'];
            $bayar = (int) ceil($total * 0.5);
            $sisa  = $total - $bayar;
            return [
                'jumlah_bayar'      => $bayar,
                'sisa_bayar'        => $sisa,
                'status_pembayaran' => 'DP',
            ];
        });
    }
}
