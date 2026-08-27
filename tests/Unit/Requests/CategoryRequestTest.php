<?php

use App\Domain\User\Models\User;
use App\Http\Requests\Category\StoreCategoryRequest;
use Illuminate\Support\Facades\Validator;

it('validates store category correctly', function () {
    $user = User::factory()->create();
    $request = StoreCategoryRequest::create('/categories', 'POST');
    $request->setUserResolver(fn () => $user);

    // valid data
    $validator = Validator::make([
        'name' => 'Gaji',
        'type' => 'income',
    ], $request->rules());
    expect($validator->passes())->toBeTrue();

    // invalid: missing name
    $validator = Validator::make([
        'type' => 'income',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('name'))->toBeTrue();

    // invalid: name too long
    $validator = Validator::make([
        'name' => str_repeat('a', 256),
        'type' => 'income',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('name'))->toBeTrue();

    // invalid: type not in income,expense
    $validator = Validator::make([
        'name' => 'Valid',
        'type' => 'invalid_type',
    ], $request->rules());
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->has('type'))->toBeTrue();
});
