<?php

namespace Database\Factories;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Transfer\Infrastructure\Persistence\Eloquent\Models\TransferModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferModel>
 */
class TransferFactory extends Factory
{
    protected $model = TransferModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Each call creates two separate accounts by default
        return [
            'from_account_id' => AccountModel::factory(),
            'to_account_id' => function (array $attributes) {
                /** @var AccountModel $fromAccount */
                $fromAccount = AccountModel::find($attributes['from_account_id']);

                return AccountModel::factory()->create(['user_id' => $fromAccount->user_id])->id;
            },
            'amount' => $this->faker->randomFloat(2, 10000, 5_000_000),
            'currency' => 'IDR',
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
