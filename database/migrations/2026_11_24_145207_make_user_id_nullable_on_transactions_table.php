<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::disableForeignKeyConstraints();

            DB::statement('CREATE TABLE transactions_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                order_id VARCHAR(255) NOT NULL UNIQUE,
                user_id INTEGER NULL,
                customer_name VARCHAR(255) NOT NULL,
                gross_amount BIGINT NOT NULL,
                payment_type VARCHAR(255) NOT NULL,
                courier VARCHAR(255) NULL,
                courier_service VARCHAR(255) NULL,
                transaction_status VARCHAR(255) NULL,
                transaction_time DATETIME NULL,
                snap_token VARCHAR(255) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )');

            DB::statement('INSERT INTO transactions_new (id, order_id, user_id, customer_name, gross_amount, payment_type, courier, courier_service, transaction_status, transaction_time, snap_token, created_at, updated_at)
                SELECT id, order_id, user_id, customer_name, gross_amount, payment_type, courier, courier_service, transaction_status, transaction_time, snap_token, created_at, updated_at FROM transactions');

            Schema::drop('transactions');
            Schema::rename('transactions_new', 'transactions');

            Schema::enableForeignKeyConstraints();
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE `transactions` MODIFY `user_id` BIGINT UNSIGNED NULL');

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::disableForeignKeyConstraints();

            DB::statement('CREATE TABLE transactions_old (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                order_id VARCHAR(255) NOT NULL UNIQUE,
                user_id INTEGER NOT NULL,
                customer_name VARCHAR(255) NOT NULL,
                gross_amount BIGINT NOT NULL,
                payment_type VARCHAR(255) NOT NULL,
                courier VARCHAR(255) NULL,
                courier_service VARCHAR(255) NULL,
                transaction_status VARCHAR(255) NULL,
                transaction_time DATETIME NULL,
                snap_token VARCHAR(255) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )');

            DB::statement('INSERT INTO transactions_old (id, order_id, user_id, customer_name, gross_amount, payment_type, courier, courier_service, transaction_status, transaction_time, snap_token, created_at, updated_at)
                SELECT id, order_id, user_id, customer_name, gross_amount, payment_type, courier, courier_service, transaction_status, transaction_time, snap_token, created_at, updated_at
                FROM transactions WHERE user_id IS NOT NULL');

            Schema::drop('transactions');
            Schema::rename('transactions_old', 'transactions');

            Schema::enableForeignKeyConstraints();
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement("DELETE FROM `transactions` WHERE `user_id` IS NULL");
        DB::statement('ALTER TABLE `transactions` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
