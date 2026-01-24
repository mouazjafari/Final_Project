<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('payment_method')->default('card'); // card, wallet, cash
            $table->string('stripe_payment_intent_id')->nullable(); // Stripe Payment Intent ID
            $table->string('stripe_session_id')->nullable();

            $table->string('stripe_charge_id')->nullable(); // Stripe Charge ID

            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');

            $table->string('currency', 3)->default('usd');

            $table->text('failure_reason')->nullable();
            $table->text('metadata')->nullable(); // JSON لأي بيانات إضافية

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
