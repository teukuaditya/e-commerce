<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index performa (Tahap 4).
 *
 * Kolom yang acak dipakai di WHERE/JOIN/ORDER BY tetapi belum berindex:
 *  - carts.user_id            → query cartCounter (SUM quantity WHERE user_id)
 *  - products.category_id     → semua listing/relasi kategori
 *  - transactions.status/time → filter dashboard (settlement, whereMonth/Year),
 *                               MIN(index bantu juga order terbaru)
 *  - transaction_details.*    → eager loading relasi detail–produk,
 *                               detail–transaksi di halaman admin
 *
 * Catatan: MySQL auto-index FK, tapi SQLite tidak — index eksplisit
 * menguntungkan kedua driver.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('transaction_status');
            $table->index('transaction_time');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->index('transaction_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('carts', fn (Blueprint $t) => $t->dropIndex(['user_id']));
        Schema::table('products', fn (Blueprint $t) => $t->dropIndex(['category_id']));
        Schema::table('transactions', function (Blueprint $t) {
            $t->dropIndex(['transaction_status']);
            $t->dropIndex(['transaction_time']);
        });
        Schema::table('transaction_details', function (Blueprint $t) {
            $t->dropIndex(['transaction_id']);
            $t->dropIndex(['product_id']);
        });
    }
};
