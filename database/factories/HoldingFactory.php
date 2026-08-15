<?php

namespace Database\Factories;

use App\Enums\HoldingType;
use App\Models\Holding;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Holding>
 */
class HoldingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'value' => fake()->randomFloat(2, 100, 25000),
            'price' => fake()->optional()->randomFloat(4, 1, 500),
            'quantity' => fake()->optional()->randomFloat(6, 0.1, 100),
            'type' => fake()->randomElement(HoldingType::cases()),
        ];
    }
}
