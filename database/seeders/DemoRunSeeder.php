<?php

// Seeder TEMPORARY hanya untuk demo run (tidak bagian dari project asli)

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoRunSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() > 0) {
            return;
        }

        User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@demo.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Demo',
            'email' => 'user@demo.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'phone' => '081234567890',
            'address' => 'Nama Penerima: User Demo, No. Telepon: 081234567890, Alamat: Jl. Demo No. 1, Destination: Jakarta Selatan, Destination ID: 1520',
        ]);

        $ctg = Category::create(['category_name' => 'Kaos Kaki', 'description' => 'Kategori demo']);

        Product::create([
            'category_id' => $ctg->id,
            'brand' => 'NGOREL',
            'title' => 'Kaos Kaki Hitam',
            'description' => 'Produk demo untuk menjalankan aplikasi.',
            'price' => 25000,
            'size' => json_encode(['S', 'M', 'L']),
            'stock' => 100,
            'weight' => 100,
        ]);

        Product::create([
            'category_id' => $ctg->id,
            'brand' => 'NGOREL',
            'title' => 'Kaos Kaki Putih',
            'description' => 'Produk demo untuk menjalankan aplikasi.',
            'price' => 30000,
            'size' => json_encode(['M', 'L', 'XL']),
            'stock' => 50,
            'weight' => 120,
        ]);
    }
}
