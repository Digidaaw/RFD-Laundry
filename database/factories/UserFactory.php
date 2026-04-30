<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'username' => fake()->unique()->userName(),
            'password' => 'password123',
            'role'     => 'kasir',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    public function kasir(): static
    {
        return $this->state(fn () => ['role' => 'kasir']);
    }
}
