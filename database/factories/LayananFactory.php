<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LayananFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->words(2, true),
            'deskripsi' => fake()->sentence(10),
            'gambar'   => json_encode(['default.jpg']),
        ];
    }
}
