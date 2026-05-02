<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'total_bottles_quantity' => fake()->numberBetween(1, 3),
            'total_price' => 0, // سيتم تحديثه في الـ callback[cite: 1]
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $items = OrderItem::factory()
                ->count(rand(1, 3))
                ->for($order)
                ->create();

            // تحديث السعر الإجمالي للطلب بناءً على مجموع العناصر[cite: 1]
            $order->update([
                'total_price' => $items->sum('bottle_total_price_with_ingredients')
            ]);
        });
    }
}
