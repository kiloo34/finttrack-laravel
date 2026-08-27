<?php

use App\Domain\Account\Models\Account;
use App\Domain\User\Models\User;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use Illuminate\Support\Facades\Validator;

it('validates store transaction correctly', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    $request = new StoreTransactionRequest;

    // valid data
    $validator = Validator::make([
        'account_id' => $account->id,
        'type' => 'expense',
        'amount' => 50000,
        'transaction_date' => '2024-01-01',
    ], $request->rules());
    expect($validator->passes())->toBeTrue();

    // invalid: amount zero or negative
    $validator = Validator::make([
        'account_id' => $account->id,
        'type' => 'expense',
        'amount' => 0,
        'transaction_date' => '2024-01-01',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('amount'))->toBeTrue();

    $validator = Validator::make([
        'account_id' => $account->id,
        'type' => 'expense',
        'amount' => -50000,
        'transaction_date' => '2024-01-01',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('amount'))->toBeTrue();

    // invalid: amount more than 14 digits
    $validator = Validator::make([
        'account_id' => $account->id,
        'type' => 'expense',
        'amount' => 1000000000000000,
        'transaction_date' => '2024-01-01',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('amount'))->toBeTrue();
});
