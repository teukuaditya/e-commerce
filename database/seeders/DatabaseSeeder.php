<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder admin default bila belum ada (temuan audit R2)
        User::firstOrCreate(
            ['email' => 'admin@ecommerce.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@ecommerce.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone' => '081234567891',
            ]
        );
    }
}
