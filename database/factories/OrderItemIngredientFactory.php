<?php

namespace Database\Factories;

use App\Models\IngredientType;
use App\Models\OrderItemIngredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemIngredientFactory extends Factory
{
    public function definition(): array
    {
        $type = IngredientType::inRandomOrder()->first() ?? IngredientType::factory()->create();
        
        $soldGrams = fake()->numberBetween(5, 50); 
        $forGramAtPurchase = (int) $type->for_gram;

        $soldTotalPrice = ($soldGrams / $forGramAtPurchase) * $type->price;

        return [
            'ingredient_type_id' => $type->id,
            'price_at_purchase' => $type->price,
            'for_gram_at_purchase' => $forGramAtPurchase, 
            'sold_quantity_grams' => $soldGrams,         
            'sold_total_price' => $soldTotalPrice,
        ];
    }
}
