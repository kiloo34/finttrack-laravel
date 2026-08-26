<?php

use App\Domain\User\Models\User;

test('api user route requires authentication', function () {
    $response = $this->getJson('/api/user');

    $response->assertStatus(401);
});

test('api user route returns authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/user');

    $response->assertStatus(200);
    $response->assertJson([
        'id' => $user->id,
        'email' => $user->email,
    ]);
});
