<?php

use App\Domain\Account\Models\Account;
use App\Domain\User\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->fromAccount = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 1000000]);
    $this->toAccount = Account::factory()->create(['user_id' => $this->user->id]);
});

it('can create a transfer and its two ledger entries', function () {
    $data = [
        'from_account_id' => $this->fromAccount->id,
        'to_account_id' => $this->toAccount->id,
        'amount' => 200000,
        'currency' => 'IDR',
        'transaction_date' => now()->format('Y-m-d'),
        'description' => 'Transfer bulanan',
    ];

    $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/transfers', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.amount', 200000);

    $transferId = $response->json('data.id');

    $this->assertDatabaseHas('transfers', [
        'from_account_id' => $this->fromAccount->id,
        'to_account_id' => $this->toAccount->id,
        'amount' => 200000,
    ]);

    // Check Debit from source
    $this->assertDatabaseHas('ledger_entries', [
        'account_id' => $this->fromAccount->id,
        'direction' => 'debit',
        'amount' => 200000,
    ]);

    // Check Credit to destination
    $this->assertDatabaseHas('ledger_entries', [
        'account_id' => $this->toAccount->id,
        'direction' => 'credit',
        'amount' => 200000,
    ]);
});

it('fails to transfer between different users accounts', function () {
    $otherAccount = Account::factory()->create(['user_id' => User::factory()->create()->id]);

    $data = [
        'from_account_id' => $this->fromAccount->id,
        'to_account_id' => $otherAccount->id,
        'amount' => 200000,
        'currency' => 'IDR',
        'transaction_date' => now()->format('Y-m-d'),
    ];

    $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/transfers', $data);

    // Should return 422 Unprocessable Entity due to validation in model/request
    $response->assertStatus(422);
});
