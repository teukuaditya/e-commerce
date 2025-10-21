<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('brand'); // Nama brand produk
            $table->string('title'); // Nama produk
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('size')->nullable(); // Menggunakan JSON untuk menyimpan ukuran
            $table->integer('stock');
            $table->integer('weight')->default(0);
            $table->json('image')->nullable(); // Menggunakan JSON untuk menyimpan nama gambar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
