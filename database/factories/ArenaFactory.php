<?php

namespace Database\Factories;

use App\Models\Arena;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Arena>
 */
class ArenaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => Str::ucfirst($this->faker->word()),
            'minimum_threshold' => $this->faker->numberBetween(0, 200000),
        ];
    }
}
