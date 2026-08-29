<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel as Account;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;

// --- Model & Relationship Tests ---

test('account belongs to a user', function () {
    $account = Account::factory()->create();

    expect($account->user)->toBeInstanceOf(User::class);
});

test('user has many accounts', function () {
    $user = User::factory()->create();
    Account::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->accounts)->toHaveCount(3);
});

// --- Fillable / Cast Tests ---

test('account has correct fillable fields', function () {
    $account = new Account;

    expect($account->getFillable())->toBe([
        'user_id',
        'name',
        'type',
        'currency',
        'account_number',
        'initial_balance',
        'is_active',
    ]);
});

test('account initial_balance is cast to decimal', function () {
    $account = Account::factory()->create(['initial_balance' => 5000000.00]);

    expect((float) $account->initial_balance)->toBe(5000000.00);
});

test('account is_active is cast to boolean', function () {
    $account = Account::factory()->create(['is_active' => true]);

    expect($account->is_active)->toBeBool()->toBeTrue();
});

// --- Soft Delete Tests ---

test('account can be soft deleted', function () {
    $account = Account::factory()->create();
    $accountId = $account->id;

    $account->delete();

    expect(Account::find($accountId))->toBeNull();
    expect(Account::withTrashed()->find($accountId))->not->toBeNull();
});

test('account can be restored after soft delete', function () {
    $account = Account::factory()->create();
    $account->delete();

    $account->restore();

    expect(Account::find($account->id))->not->toBeNull();
});
