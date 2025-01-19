<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categories;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categories_id' => Categories::inRandomOrder()->first()->id,
            'name' => fake()->name(),
            'price' => fake()->randomNumber(3, true),
            'quantity' => fake()->randomDigit(),
            'description' => fake()->paragraph(),
        ];
    }
}
