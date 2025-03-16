<?php

namespace Database\Factories;

use App\Models\Revenue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Revenue>
 */
class RevenueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'occurs_at' => $this->faker->dateTimeBetween('-1 year'),
            'signed_amount' => fake()->randomFloat(2, max: 150000),
            'invoiced_amount' => fake()->randomFloat(2, max: 150000),
        ];
    }
}
