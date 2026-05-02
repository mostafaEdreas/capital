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
        Schema::create('order_item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('ingredient_type_id')->constrained('ingredient_types');
            $table->decimal('price', 10, 2);
            $table->integer('for_gram')->default(1);
            $table->integer('sold_quantity_grams')->default(0);
            $table->decimal('sold_total_price', 10, 2);
            $table->timestamps();
        });
    }
};
