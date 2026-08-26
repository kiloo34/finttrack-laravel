<?php

use App\Domain\Category\Models\Category;
use App\Domain\User\Models\User;

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
    $systemCategory = Category::factory()->system()->create();

    // Attempting to delete should throw an exception
    expect(fn() => $systemCategory->delete())->toThrow(\Exception::class, 'System categories cannot be deleted.');

    // Attempting to modify name should throw an exception
    $systemCategory->name = 'Hacked Name';
    expect(fn() => $systemCategory->save())->toThrow(\Exception::class, 'System categories cannot be modified.');
});
