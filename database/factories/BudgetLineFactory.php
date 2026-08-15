<?php

namespace Database\Factories;

use App\Models\BudgetLine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BudgetLine>
 */
class BudgetLineFactory extends Factory
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
            'name' => fake()->randomElement(['Dagligvarer', 'Strøm', 'Felleskostnader', 'Trening', 'Annet']),
            'daily' => null,
            'weekly' => null,
            'monthly' => fake()->randomFloat(2, 100, 4000),
            'other_monthly' => null,
            'yearly' => null,
            'note' => fake()->optional()->sentence(),
        ];
    }
}
