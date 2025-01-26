<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoreAddress>
 */
class StoreAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::inRandomOrder()->first()->id,
            'main' => '1',
            'branch_number' => 1,
            'street' => fake()->streetAddress(),
            'barangay' => fake()->streetAddress(),
            'city' => fake()->address(),
            'province' => fake()->address(),
            'region' => fake()->address(),
            'zipcode' => fake()->address(),
        ];
    }
}
