<?php

namespace Database\Factories;

use App\Domain\Account\Models\Account;
use App\Domain\Transfer\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transfer>
 */
class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Each call creates two separate accounts by default
        return [
            'from_account_id' => Account::factory(),
            'to_account_id'   => Account::factory(),
            'amount'          => $this->faker->randomFloat(2, 10000, 5_000_000),
            'currency'        => 'IDR',
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'description'     => $this->faker->optional()->sentence(),
        ];
    }
}
