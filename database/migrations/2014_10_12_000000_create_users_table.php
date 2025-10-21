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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Kolom ID
            $table->string('name'); // Kolom Nama
            $table->string('email')->unique(); // Kolom Email
            $table->timestamp('email_verified_at')->nullable(); // Kolom Verifikasi Email
            $table->string('password'); // Kolom Password
            $table->rememberToken(); // Kolom Remember Token
            $table->string('phone')->nullable(); // Kolom Phone (opsional)
            $table->enum('role', ['user', 'admin'])->default('user'); // Kolom Role
            $table->text('address')->nullable(); // Kolom Address (opsional)
            $table->timestamps(); // Kolom Timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users'); // Menghapus tabel users
    }
};
