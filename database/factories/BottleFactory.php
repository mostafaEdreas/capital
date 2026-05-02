<?php

namespace Database\Factories;

use App\Models\Bottle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bottle>
 */
class BottleFactory extends Factory
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
            'size' => fake()->numberBetween(100, 2000),
            'price' => fake()->randomFloat(2, 10, 100),
        ];
    }
}
