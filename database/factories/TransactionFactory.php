<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Enums\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
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
            'booked_on' => fake()->date(),
            'amount' => fake()->randomFloat(2, 1, 2500),
            'category' => null,
            'payee' => fake()->company(),
            'payment_method' => fake()->optional()->randomElement(PaymentMethod::cases()),
            'note' => fake()->optional()->sentence(),
        ];
    }

    public function categorized(?Category $category = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'category' => $category ?? fake()->randomElement(Category::cases()),
        ]);
    }
}
