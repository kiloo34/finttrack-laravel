<?php

use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;

test('user can login successfully with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'johndoe@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'johndoe@example.com',
        'password' => 'password123',
        'device_name' => 'iphone-15',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ])
        ->assertJsonStructure([
            'status',
            'data' => [
                'token',
                'user' => ['id', 'name', 'email'],
            ],
        ]);

    expect($response->json('data.token'))->toBeString()->not->toBeEmpty();
});

test('login fails with invalid password and returns 422', function () {
    User::factory()->create([
        'email' => 'johndoe@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'johndoe@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The provided credentials do not match our records.',
            'errors' => [
                'email' => [
                    'The provided credentials do not match our records.',
                ],
            ],
        ]);
});

test('login fails with non-existent email and returns 422', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The provided credentials do not match our records.',
            'errors' => [
                'email' => [
                    'The provided credentials do not match our records.',
                ],
            ],
        ]);
});

test('authenticated user can get own profile via me endpoint', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/auth/me');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

test('logout revokes current access token and token cannot be used again', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $loginResponse = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $token = $loginResponse->json('data.token');

    $logoutResponse = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/auth/logout');

    $logoutResponse->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);

    // Reset in-memory guard cache between requests in test process
    app('auth')->forgetGuards();

    // Attempt to access protected endpoint with revoked token
    $meResponse = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/auth/me');

    $meResponse->assertStatus(401);
});

test('protected auth endpoints return 401 when unauthenticated', function () {
    $this->getJson('/api/auth/me')->assertStatus(401);
    $this->postJson('/api/auth/logout')->assertStatus(401);
});
