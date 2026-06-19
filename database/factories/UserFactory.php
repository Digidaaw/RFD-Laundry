<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password123'),
            'role' => $this->faker->randomElement(['admin', 'kasir']),
            'remember_token' => null,
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
                'username' => 'admin',
                'name' => 'Admin RFD',
                'password' => Hash::make('admin123'),
            ];
        });
    }

    public function kasir()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'kasir',
                'username' => 'kasir1',
                'name' => 'Kasir RFD',
                'password' => Hash::make('kasir123'),
            ];
        });
    }
}
