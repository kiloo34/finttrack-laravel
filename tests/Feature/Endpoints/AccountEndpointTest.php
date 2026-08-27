<?php

use App\Domain\Account\Models\Account;
use App\Domain\User\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists user accounts', function () {
    $account = Account::factory()->create(['user_id' => $this->user->id]);
    $otherAccount = Account::factory()->create(['user_id' => User::factory()->create()->id]);

    $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/accounts');

    $response->assertStatus(200)
        ->assertJsonFragment(['id' => $account->id])
        ->assertJsonMissing(['id' => $otherAccount->id]);
});

it('can create an account', function () {
    $data = [
        'name' => 'Gopay',
        'type' => 'e_wallet',
        'currency' => 'IDR',
        'initial_balance' => 50000,
        'is_active' => true,
    ];

    $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/accounts', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Gopay')
        ->assertJsonPath('data.initial_balance', 50000);

    $this->assertDatabaseHas('accounts', [
        'name' => 'Gopay',
        'user_id' => $this->user->id,
    ]);
});

it('can update an account', function () {
    $account = Account::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Lama',
    ]);

    $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/accounts/{$account->id}", [
        'name' => 'Baru',
        'type' => 'bank',
        'currency' => 'IDR',
        'initial_balance' => 0,
        'is_active' => true,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Baru');

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'name' => 'Baru',
    ]);
});

it('cannot update other users account', function () {
    $otherAccount = Account::factory()->create(['user_id' => User::factory()->create()->id]);

    $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/accounts/{$otherAccount->id}", [
        'name' => 'Baru',
        'type' => 'bank',
        'currency' => 'IDR',
        'initial_balance' => 0,
        'is_active' => true,
    ]);

    $response->assertStatus(403);
});

it('can delete an account', function () {
    $account = Account::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/accounts/{$account->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('accounts', ['id' => $account->id]);
});
