<?php

use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel as Category;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;
use Illuminate\Database\QueryException;

// --- Model & Relationship Tests ---

test('category belongs to a user', function () {
    $category = Category::factory()->create();

    expect($category->user)->toBeInstanceOf(User::class);
});

test('system category has no user', function () {
    $category = Category::factory()->system()->create();

    expect($category->user_id)->toBeNull();
    expect($category->is_system)->toBeTrue();
});

test('user has many categories', function () {
    $user = User::factory()->create();
    Category::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->categories)->toHaveCount(3);
});

// --- Fillable / Cast Tests ---

test('category has correct fillable fields', function () {
    $category = new Category;

    expect($category->getFillable())->toBe([
        'user_id',
        'name',
        'type',
        'is_system',
    ]);
});

test('category is_system is cast to boolean', function () {
    $category = Category::factory()->create(['is_system' => false]);

    expect($category->is_system)->toBeBool()->toBeFalse();
});

// --- Unique Constraint Test ---

test('user cannot have duplicate category names', function () {
    $user = User::factory()->create();

    Category::factory()->create(['user_id' => $user->id, 'name' => 'Makan']);

    expect(fn () => Category::factory()->create(['user_id' => $user->id, 'name' => 'Makan']))
        ->toThrow(QueryException::class);
});

// --- Scope Tests ---

test('forUser scope returns only categories belonging to a specific user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Category::factory()->count(2)->create(['user_id' => $user->id]);
    Category::factory()->count(3)->create(['user_id' => $otherUser->id]);

    $results = Category::forUser($user->id)->get();

    expect($results)->toHaveCount(2);
    expect($results->pluck('user_id')->unique()->first())->toBe($user->id);
});

test('system scope returns only system categories', function () {
    Category::factory()->system()->count(2)->create();
    Category::factory()->count(3)->create(); // user-owned categories

    $results = Category::system()->get();

    expect($results)->toHaveCount(2);
    expect($results->every(fn ($c) => $c->is_system === true))->toBeTrue();
});
