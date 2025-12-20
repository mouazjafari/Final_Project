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
        Schema::create('design_option_selected', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_order_id')->constrained('design_order')->onDelete('cascade');
            $table->foreignId('design_option_id')->constrained('design_options')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_option_selected');
    }
};
