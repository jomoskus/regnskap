<?php

namespace Database\Factories;

use App\Enums\FigureSection;
use App\Models\MonthlyFigure;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonthlyFigure>
 */
class MonthlyFigureFactory extends Factory
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
            'month' => now()->startOfMonth()->toDateString(),
            'section' => fake()->randomElement(FigureSection::cases()),
            'item' => fake()->unique()->words(2, true),
            'amount' => fake()->randomFloat(2, 10, 8000),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
