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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('bottle_id')->constrained();
            $table->decimal('bottle_price', 10, 2);
            $table->integer('bottle_quantity')->default(1);
            $table->decimal('bottle_total_price', 10, 2)->default(0);
            $table->decimal('bottle_total_price_with_ingredients', 10, 2)->default(0);
            $table->index(['order_id', 'bottle_id'], 'idx_order_bottle');
            $table->index('bottle_id', 'idx_bottle');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
