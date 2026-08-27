<?php

use App\Http\Requests\Account\StoreAccountRequest;
use Illuminate\Support\Facades\Validator;

it('validates store account correctly', function () {
    $request = new StoreAccountRequest;

    // valid data
    $validator = Validator::make([
        'name' => 'BCA',
        'type' => 'bank',
        'currency' => 'IDR',
        'initial_balance' => 1000000,
        'is_active' => true,
    ], $request->rules());
    expect($validator->passes())->toBeTrue();

    // invalid: missing name
    $validator = Validator::make([
        'type' => 'bank',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('name'))->toBeTrue();

    // invalid: initial_balance negative
    $validator = Validator::make([
        'name' => 'BCA',
        'type' => 'bank',
        'initial_balance' => -100,
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('initial_balance'))->toBeTrue();

    // invalid: type not allowed
    $validator = Validator::make([
        'name' => 'BCA',
        'type' => 'crypto',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('type'))->toBeTrue();
});
