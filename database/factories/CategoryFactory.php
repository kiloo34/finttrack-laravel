<?php

namespace Database\Factories;

use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoryModel>
 */
class CategoryFactory extends Factory
{
    protected $model = CategoryModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => UserModel::factory(),
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
