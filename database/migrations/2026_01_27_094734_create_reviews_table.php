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
            $table->foreignId(column: 'order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId(column: 'user_id')->constrained('users')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->enum('rating', [1, 2, 3, 4, 5])->nullable(); // Assuming rating is between 1-5
            $table->timestamps();
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
