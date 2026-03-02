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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products_catalog')->cascadeOnDelete();
            $table->integer('quantity');
            $table->float('unit_price');
            $table->float('subtotal');
            $table->float('discount')->nullable()->default(0);
            $table->float('tax');
            $table->float('total');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
