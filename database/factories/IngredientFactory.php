<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\IngredientType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
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
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Ingredient $ingredient) {
            IngredientType::factory()->count(rand(1,10))->create(['ingredient_id' => $ingredient->id]);
        });
    }
}
