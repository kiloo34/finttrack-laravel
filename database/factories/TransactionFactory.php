<?php

namespace Database\Factories;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionModel>
 */
class TransactionFactory extends Factory
{
    protected $model = TransactionModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => AccountModel::factory(),
            'category_id' => CategoryModel::factory()->system(),
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
