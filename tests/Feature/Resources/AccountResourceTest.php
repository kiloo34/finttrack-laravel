<?php

use App\Domain\Account\Models\Account;
use App\Domain\User\Models\User;
use App\Http\Resources\AccountResource;

it('formats account resource correctly including balance', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'name' => 'BCA',
        'type' => 'bank',
        'currency' => 'IDR',
        'initial_balance' => 1000000,
        'is_active' => true,
    ]);

    $resource = new AccountResource($account);
    $array = $resource->toArray(request());

    expect($array)
        ->toHaveKey('id', $account->id)
        ->toHaveKey('name', 'BCA')
        ->toHaveKey('type', 'bank')
        ->toHaveKey('currency', 'IDR')
        ->toHaveKey('initial_balance', 1000000.0)
        ->toHaveKey('current_balance', 1000000.0) // because there are no ledger entries, it should equal initial
        ->toHaveKey('is_active', true)
        ->toHaveKey('created_at')
        ->toHaveKey('updated_at')
        ->not->toHaveKey('user_id');
});
