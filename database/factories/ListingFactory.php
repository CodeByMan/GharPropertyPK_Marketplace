<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'beds' => fake()->numberBetween(1, 7),
            'baths' => fake()->numberBetween(1, 7),
            'area' => fake()->numberBetween(30, 400),
            'city' => fake()->city(),
            'code' => fake()->postcode(),
            'street' => fake()->streetName(),
            'street_nr' => (string) fake()->numberBetween(10, 200),
            'price' => fake()->numberBetween(50_000, 5_000_000),
            'user_id' => User::factory(),
        ];
    }
}
