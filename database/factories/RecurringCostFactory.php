<?php

namespace Database\Factories;

use App\Enums\RecurringInterval;
use App\Models\RecurringCost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecurringCost>
 */
class RecurringCostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $interval = fake()->randomElement(RecurringInterval::cases());
        $amount = fake()->randomFloat(2, 50, 2000);

        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'amount' => $amount,
            'currency' => 'NOK',
            'monthly_nok' => RecurringCost::monthlyEquivalent((string) $amount, $interval),
            'renews_on' => fake()->optional()->date(),
            'interval' => $interval,
            'payment_method' => fake()->optional()->randomElement(['Kredittkort', 'AvtaleGiro']),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
