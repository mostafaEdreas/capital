<?php

namespace Database\Factories;

use App\Models\IngredientType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IngredientType>
 */
class IngredientTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'price' => fake()->randomFloat(2, 10, 100),
            'for_gram' => fake()->numberBetween(1, 100),
        ];
    }
}
