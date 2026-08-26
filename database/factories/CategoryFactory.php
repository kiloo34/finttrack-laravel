<?php

namespace Database\Factories;

use App\Domain\Category\Models\Category;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->unique()->word(),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'is_system' => false,
        ];
    }

    /**
     * Indicate that the category is a system category (no user).
     */
    public function system(): static
    {
        return $this->state(fn () => [
            'user_id' => null,
            'is_system' => true,
        ]);
    }
}
