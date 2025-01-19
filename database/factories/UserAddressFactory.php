<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'type' => 'work',
            'default' => '1',
            'street' => fake()->streetAddress(),
            'barangay' => fake()->streetAddress(),
            'city' => fake()->address(),
            'province' => fake()->address(),
            'region' => fake()->address(),
            'zipcode' => fake()->address(),
        ];
    }
}
