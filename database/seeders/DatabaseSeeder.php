<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $username = env('SEEDER_ADMIN_USERNAME');
        $password = env('SEEDER_ADMIN_PASSWORD');

        if ($username && $password) {
            \App\Models\User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => 'Tanto Admin',
                    'password' => $password,
                    'role' => 'admin',
                    'is_active' => true,
                ]
            );
        }
    }
}
