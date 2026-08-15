<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Enums\Confidence;
use App\Models\CategoryRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryRule>
 */
class CategoryRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payee' => fake()->company(),
            'category' => fake()->randomElement(Category::cases()),
            'confidence' => Confidence::Sannsynlig,
            'matches' => fake()->numberBetween(0, 20),
        ];
    }
}
