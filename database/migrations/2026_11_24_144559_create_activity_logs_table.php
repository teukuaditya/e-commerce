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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('title');
            $table->text('details')->nullable();
            $table->string('badge', 50)->nullable();
            $table->string('badge_color', 20)->nullable();
            $table->string('badge_bg', 40)->nullable();
            $table->string('icon', 60)->nullable();
            $table->string('icon_color', 20)->nullable();
            $table->string('icon_bg', 40)->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
