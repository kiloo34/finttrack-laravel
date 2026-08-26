<?php

namespace Database\Factories;

use App\Domain\Account\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Domain\User\Models\User::factory(),
            'name' => $this->faker->words(2, true),
            'type' => $this->faker->randomElement(['cash', 'bank', 'e_wallet', 'credit_card', 'investment', 'other']),
            'currency' => 'IDR',
            'initial_balance' => $this->faker->randomFloat(2, 0, 10_000_000),
            'is_active' => true,
        ];
    }
}
