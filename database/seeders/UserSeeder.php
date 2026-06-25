<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'password' => 'admin123',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        \App\Models\User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir',
                'password' => 'kasir123',
                'role' => 'kasir',
                'is_active' => true,
            ]
        );
    }
}
