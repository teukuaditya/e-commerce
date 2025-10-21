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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('order_id')->unique();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('customer_name');
        $table->bigInteger('gross_amount');
        $table->enum('payment_type', ['credit_card', 'bank_transfer', 'ewallet', 'gopay', 'qris', 'cstore', 'kredivo', 'akulaku']);
        $table->enum('courier', ['JNE', 'POS', 'SICEPAT'])->nullable();
        $table->string('courier_service')->nullable();
        $table->string('transaction_status')->nullable();
        $table->timestamp('transaction_time')->nullable();
        $table->string('snap_token')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
