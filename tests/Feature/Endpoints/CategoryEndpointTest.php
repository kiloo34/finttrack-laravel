<?php

use App\Domain\Category\Models\Category;
use App\Domain\User\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists user and system categories but hides other users categories', function () {
    $systemCategory = Category::factory()->create(['user_id' => null, 'is_system' => true]);
    $userCategory = Category::factory()->create(['user_id' => $this->user->id, 'is_system' => false]);
    $otherUserCategory = Category::factory()->create(['user_id' => User::factory()->create()->id, 'is_system' => false]);

    $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/categories');

    $response->assertStatus(200)
        ->assertJsonFragment(['id' => $systemCategory->id])
        ->assertJsonFragment(['id' => $userCategory->id])
        ->assertJsonMissing(['id' => $otherUserCategory->id]);
});

it('can create a category', function () {
    $data = [
        'name' => 'Belanja',
        'type' => 'expense',
    ];

    $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/categories', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Belanja')
        ->assertJsonPath('data.type', 'expense');

    $this->assertDatabaseHas('categories', [
        'name' => 'Belanja',
        'user_id' => $this->user->id,
        'is_system' => false,
    ]);
});

it('can update a category', function () {
    $category = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Lama',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/categories/{$category->id}", [
        'name' => 'Baru',
        'type' => 'income',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Baru');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Baru',
    ]);
});

it('cannot update a system category', function () {
    $systemCategory = Category::factory()->create(['user_id' => null, 'is_system' => true]);

    $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/categories/{$systemCategory->id}", [
        'name' => 'Baru',
        'type' => 'expense',
    ]);

    $response->assertStatus(403);
});

it('can delete a category', function () {
    $category = Category::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/categories/{$category->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('cannot delete a system category', function () {
    $systemCategory = Category::factory()->create(['user_id' => null, 'is_system' => true]);

    $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/categories/{$systemCategory->id}");

    $response->assertStatus(403);
});
