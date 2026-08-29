<?php

use App\Modules\Category\Domain\Exceptions\SystemCategoryModificationException;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel as Category;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;

// --- Category Business Rules Tests ---

test('availableFor scope returns system categories and user specific categories', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Create a system category
    $systemCategory = Category::factory()->system()->create();

    // Create a category for User A
    $categoryA = Category::factory()->create(['user_id' => $userA->id]);

    // Create a category for User B
    $categoryB = Category::factory()->create(['user_id' => $userB->id]);

    // Get available categories for User A
    $availableForA = Category::availableFor($userA->id)->get();

    // User A should see the system category and their own category, but not User B's
    expect($availableForA->pluck('id')->toArray())
        ->toContain($systemCategory->id)
        ->toContain($categoryA->id)
        ->not->toContain($categoryB->id);

    expect($availableForA->count())->toBeGreaterThanOrEqual(2);
});

test('system categories cannot be deleted or modified', function () {
    $systemCategory = App\Modules\Category\Domain\Entities\Category::create(
        userId: 0,
        name: 'System Category',
        type: 'expense'
    );
    // Since create() makes it non-system by default, we must reconstitute it to make it system
    $systemCategory = App\Modules\Category\Domain\Entities\Category::reconstitute(
        id: 1,
        userId: null,
        name: 'System Category',
        type: 'expense',
        isSystem: true,
        createdAt: new DateTimeImmutable,
        updatedAt: new DateTimeImmutable
    );

    // Attempting to delete should throw an exception
    expect(fn () => $systemCategory->ensureCanBeDeleted())
        ->toThrow(SystemCategoryModificationException::class, 'System categories cannot be deleted.');

    // Attempting to modify name should throw an exception
    expect(fn () => $systemCategory->update('Hacked Name', 'expense'))
        ->toThrow(SystemCategoryModificationException::class, 'System categories cannot be modified.');
});
