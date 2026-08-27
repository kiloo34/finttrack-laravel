<?php

use App\Domain\Account\Models\Account;
use App\Domain\User\Models\User;
use App\Http\Requests\Transfer\StoreTransferRequest;
use Illuminate\Support\Facades\Validator;

it('validates store transfer correctly', function () {
    $user = User::factory()->create();
    $account1 = Account::factory()->create(['user_id' => $user->id]);
    $account2 = Account::factory()->create(['user_id' => $user->id]);

    $request = new StoreTransferRequest;

    // valid data
    $validator = Validator::make([
        'from_account_id' => $account1->id,
        'to_account_id' => $account2->id,
        'amount' => 50000,
        'currency' => 'IDR',
        'transaction_date' => '2024-01-01',
    ], $request->rules());
    expect($validator->passes())->toBeTrue();

    // invalid: from and to account are same
    $validator = Validator::make([
        'from_account_id' => $account1->id,
        'to_account_id' => $account1->id,
        'amount' => 50000,
        'currency' => 'IDR',
        'transaction_date' => '2024-01-01',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('to_account_id'))->toBeTrue();

    // invalid: negative amount
    $validator = Validator::make([
        'from_account_id' => $account1->id,
        'to_account_id' => $account2->id,
        'amount' => -100,
        'currency' => 'IDR',
        'transaction_date' => '2024-01-01',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('amount'))->toBeTrue();
});
