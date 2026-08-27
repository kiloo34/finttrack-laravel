<?php

use App\Domain\Account\Models\Account;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\User\Models\User;
use App\Http\Resources\TransferResource;

it('formats transfer resource correctly', function () {
    $user = User::factory()->create();
    $fromAccount = Account::factory()->create(['user_id' => $user->id]);
    $toAccount = Account::factory()->create(['user_id' => $user->id]);

    $transfer = Transfer::factory()->create([
        'from_account_id' => $fromAccount->id,
        'to_account_id' => $toAccount->id,
        'amount' => 50000,
        'transaction_date' => now(),
    ]);

    $resource = new TransferResource($transfer);
    $array = $resource->resolve();

    expect($array)
        ->toHaveKey('id', $transfer->id)
        ->toHaveKey('amount', 50000.0)
        ->toHaveKey('transaction_date', $transfer->transaction_date->format('Y-m-d'))
        ->not->toHaveKey('source_account')
        ->not->toHaveKey('destination_account');
});

it('includes loaded relationships in transfer resource', function () {
    $user = User::factory()->create();
    $fromAccount = Account::factory()->create(['user_id' => $user->id]);
    $toAccount = Account::factory()->create(['user_id' => $user->id]);

    $transfer = Transfer::factory()->create([
        'from_account_id' => $fromAccount->id,
        'to_account_id' => $toAccount->id,
    ]);

    $transfer->load(['fromAccount', 'toAccount']);

    $resource = new TransferResource($transfer);
    $array = $resource->resolve();

    expect($array)
        ->toHaveKey('source_account')
        ->toHaveKey('destination_account');

    expect($array['source_account']['id'])->toBe($fromAccount->id);
    expect($array['destination_account']['id'])->toBe($toAccount->id);
});
