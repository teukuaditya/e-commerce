<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreDataSeeder extends Seeder
{
    public function run(): void
    {
        // Add sample customers if few users exist
        if (User::where('role', 'user')->count() <= 1) {
            $customers = [
                ['name' => 'Alex Pratama', 'email' => 'alex.pratama@example.com', 'phone' => '081298765432', 'address' => 'Jl. Senopati No. 12, Jakarta Selatan', 'created_at' => now()->subMinutes(42)],
                ['name' => 'Siti Rahma', 'email' => 'siti.rahma@example.com', 'phone' => '081387654321', 'address' => 'Jl. Dago Asri No. 45, Bandung', 'created_at' => now()->subHours(3)],
                ['name' => 'Budi Santoso', 'email' => 'budi.santoso@example.com', 'phone' => '082176543210', 'address' => 'Jl. Pemuda No. 88, Surabaya', 'created_at' => now()->subDays(1)],
                ['name' => 'Maya Putri', 'email' => 'maya.putri@example.com', 'phone' => '085265432109', 'address' => 'Jl. Malioboro No. 15, Yogyakarta', 'created_at' => now()->subDays(2)],
            ];

            foreach ($customers as $c) {
                User::firstOrCreate(
                    ['email' => $c['email']],
                    [
                        'name' => $c['name'],
                        'password' => Hash::make('password123'),
                        'role' => 'user',
                        'phone' => $c['phone'],
                        'address' => $c['address'],
                        'created_at' => $c['created_at'],
                        'updated_at' => $c['created_at'],
                    ]
                );
            }
        }

        // Add sample transactions if none exist
        if (Transaction::count() === 0) {
            $user = User::where('role', 'user')->first() ?? User::first();
            $products = Product::all();
            if ($products->isEmpty()) {
                return;
            }

            $currentYear = now()->year;
            $currentMonth = now()->month;

            // Monthly settlement history for current year (Jan to currentMonth)
            $monthlyRevenues = [
                1 => 6200000,
                2 => 7800000,
                3 => 9400000,
                4 => 8500000,
                5 => 8800000,
                6 => 10200000,
                7 => 12500000,
                8 => 14100000,
                9 => 15600000,
                10 => 17400000,
                11 => 12450000,
                12 => 21200000,
            ];

            // Seed historical monthly transactions
            foreach ($monthlyRevenues as $m => $amount) {
                if ($m < $currentMonth) {
                    $trxDate = Carbon::create($currentYear, $m, 15, 14, 30, 0);
                    $trx = Transaction::create([
                        'order_id' => 'DRVN-' . $currentYear . sprintf('%02d', $m) . '-01',
                        'user_id' => $user->id,
                        'customer_name' => 'Customer DRVN Bulan ' . $m,
                        'gross_amount' => $amount,
                        'payment_type' => 'bank_transfer',
                        'courier' => 'JNE',
                        'courier_service' => 'REG',
                        'transaction_status' => 'settlement',
                        'transaction_time' => $trxDate,
                        'created_at' => $trxDate,
                        'updated_at' => $trxDate,
                    ]);

                    $prod = $products->random();
                    TransactionDetail::create([
                        'transaction_id' => $trx->id,
                        'product_id' => $prod->id,
                        'quantity' => 2,
                        'price' => $prod->price,
                        'subtotal' => $amount,
                    ]);
                }
            }

            // Seed recent transactions for the current month / week
            $recentSeeds = [
                [
                    'order_id' => 'DRVN-8942',
                    'customer_name' => 'Alex Pratama',
                    'gross_amount' => 850000,
                    'payment_type' => 'gopay',
                    'courier' => 'JNE',
                    'courier_service' => 'REG',
                    'transaction_status' => 'pending',
                    'time' => now()->subMinutes(2),
                ],
                [
                    'order_id' => 'DRVN-8939',
                    'customer_name' => 'Siti Rahma',
                    'gross_amount' => 1420000,
                    'payment_type' => 'bank_transfer',
                    'courier' => 'SICEPAT',
                    'courier_service' => 'BEST',
                    'transaction_status' => 'settlement',
                    'time' => now()->subMinutes(15),
                ],
                [
                    'order_id' => 'DRVN-8935',
                    'customer_name' => 'Budi Santoso',
                    'gross_amount' => 650000,
                    'payment_type' => 'qris',
                    'courier' => 'POS',
                    'courier_service' => 'KILAT',
                    'transaction_status' => 'settlement',
                    'time' => now()->subHours(2),
                ],
                [
                    'order_id' => 'DRVN-8931',
                    'customer_name' => 'Maya Putri',
                    'gross_amount' => 2100000,
                    'payment_type' => 'bank_transfer',
                    'courier' => 'JNE',
                    'courier_service' => 'YES',
                    'transaction_status' => 'pending',
                    'time' => now()->subHours(5),
                ],
                [
                    'order_id' => 'DRVN-8928',
                    'customer_name' => 'Rian Ardiansyah',
                    'gross_amount' => 450000,
                    'payment_type' => 'ewallet',
                    'courier' => 'SICEPAT',
                    'courier_service' => 'GOKIL',
                    'transaction_status' => 'settlement',
                    'time' => now()->subDay(),
                ],
                [
                    'order_id' => 'DRVN-8921',
                    'customer_name' => 'Dwi Handayani',
                    'gross_amount' => 1750000,
                    'payment_type' => 'bank_transfer',
                    'courier' => 'JNE',
                    'courier_service' => 'REG',
                    'transaction_status' => 'settlement',
                    'time' => now()->subDays(2),
                ],
                [
                    'order_id' => 'DRVN-8915',
                    'customer_name' => 'Eko Prasetyo',
                    'gross_amount' => 980000,
                    'payment_type' => 'qris',
                    'courier' => 'POS',
                    'courier_service' => 'REG',
                    'transaction_status' => 'pending',
                    'time' => now()->subDays(3),
                ],
                [
                    'order_id' => 'DRVN-8908',
                    'customer_name' => 'Farhan Nugraha',
                    'gross_amount' => 3250000,
                    'payment_type' => 'credit_card',
                    'courier' => 'JNE',
                    'courier_service' => 'YES',
                    'transaction_status' => 'settlement',
                    'time' => now()->subDays(4),
                ],
                [
                    'order_id' => 'DRVN-8899',
                    'customer_name' => 'Gita Gutawa',
                    'gross_amount' => 1200000,
                    'payment_type' => 'bank_transfer',
                    'courier' => 'SICEPAT',
                    'courier_service' => 'SIUNTUNG',
                    'transaction_status' => 'settlement',
                    'time' => now()->subDays(5),
                ],
            ];

            foreach ($recentSeeds as $s) {
                $trx = Transaction::create([
                    'order_id' => $s['order_id'],
                    'user_id' => $user->id,
                    'customer_name' => $s['customer_name'],
                    'gross_amount' => $s['gross_amount'],
                    'payment_type' => $s['payment_type'],
                    'courier' => $s['courier'],
                    'courier_service' => $s['courier_service'],
                    'transaction_status' => $s['transaction_status'],
                    'transaction_time' => $s['time'],
                    'created_at' => $s['time'],
                    'updated_at' => $s['time'],
                ]);

                $prod = $products->random();
                TransactionDetail::create([
                    'transaction_id' => $trx->id,
                    'product_id' => $prod->id,
                    'quantity' => 1,
                    'price' => $s['gross_amount'],
                    'subtotal' => $s['gross_amount'],
                ]);
            }
        }
    }
}
