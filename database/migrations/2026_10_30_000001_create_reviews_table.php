<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // ondelete('restrict') — riwayat review tidak ikut hilang saat produk dihapus
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedTinyInteger('rating'); // 1–5 bintang
            $table->text('comment');
            $table->timestamps();

            // Satu user hanya boleh punya satu review per produk
            // (review tetap bisa ia perbarui/di-update)
            $table->unique(['user_id', 'product_id']);

            $table->index('product_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
