<?php

namespace Database\Factories;

use App\Models\Bottle;
use App\Models\OrderItem;
use App\Models\OrderItemIngredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $bottle = Bottle::inRandomOrder()->first() ?? Bottle::factory()->create();
        $quantity = fake()->numberBetween(1, 5);
        $totalPrice = $bottle->price * $quantity;

        return [
            'bottle_id' => $bottle->id,
            'bottle_price' => $bottle->price,
            'bottle_quantity' => $quantity,
            'bottle_total_price' => $totalPrice,
            'bottle_total_price_with_ingredients' => $totalPrice,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (OrderItem $item) {
            $ingredients = OrderItemIngredient::factory()
                ->count(rand(1, 3))
                ->create(['order_item_id' => $item->id]);

            $item->update([
                'bottle_total_price_with_ingredients' => $item->bottle_total_price + ($ingredients->sum('sold_total_price') * $item->bottle_quantity)
            ]);
        });
    }
}