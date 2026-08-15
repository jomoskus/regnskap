<?php

namespace Database\Factories;

use App\Models\HousingPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HousingPlan>
 */
class HousingPlanFactory extends Factory
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
            'horizon_year' => fake()->numberBetween(2026, 2035),
            'sale_price' => fake()->optional()->randomFloat(2, 100000, 900000),
            'mortgage_on_sold' => fake()->optional()->randomFloat(2, 10000, 400000),
            'equity_from_sale' => fake()->optional()->randomFloat(2, 10000, 400000),
            'saving_per_year' => fake()->optional()->randomFloat(2, 1000, 80000),
            'saved_total' => fake()->optional()->randomFloat(2, 1000, 200000),
            'expected_income' => fake()->optional()->randomFloat(2, 20000, 200000),
            'possible_loan' => fake()->optional()->randomFloat(2, 100000, 800000),
            'student_loan' => fake()->optional()->randomFloat(2, 0, 150000),
            'mortgage' => fake()->optional()->randomFloat(2, 0, 600000),
            'purchase_price' => fake()->optional()->randomFloat(2, 200000, 1200000),
        ];
    }
}
