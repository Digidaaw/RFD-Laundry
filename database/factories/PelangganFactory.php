<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PelangganFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'   => fake()->name(),
            'kontak' => fake()->unique()->numerify('08##########'),
        ];
    }
}
