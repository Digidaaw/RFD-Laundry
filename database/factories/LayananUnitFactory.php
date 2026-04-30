<?php

namespace Database\Factories;

use App\Models\Layanan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayananUnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'layanan_id'  => Layanan::factory(),
            'unit_satuan' => fake()->randomElement(['kg', 'pcs', 'meter']),
            'harga'       => fake()->numberBetween(5000, 50000),
        ];
    }
}
