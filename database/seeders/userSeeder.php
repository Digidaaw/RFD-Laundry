<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
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
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
            ]
        );
    }
}
