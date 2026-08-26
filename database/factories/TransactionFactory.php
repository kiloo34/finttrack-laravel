<?php

namespace Database\Factories;

use App\Domain\Account\Models\Account;
use App\Domain\Category\Models\Category;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'category_id' => Category::factory(),
            'type' => $this->faker->randomElement(['income', 'expense', 'adjustment']),
            'amount' => $this->faker->randomFloat(2, 1000, 5_000_000),
            'currency' => 'IDR',
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'description' => $this->faker->optional()->sentence(),
            'reference' => $this->faker->optional()->bothify('REF-####'),
            'status' => 'completed',
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => 'income']);
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => 'expense']);
    }

    public function adjustment(): static
    {
        return $this->state(fn () => ['type' => 'adjustment']);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function voided(): static
    {
        return $this->state(fn () => ['status' => 'voided']);
    }
}
