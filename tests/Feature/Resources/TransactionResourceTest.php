<?php

use App\Domain\Account\Models\Account;
use App\Domain\Category\Models\Category;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\User\Models\User;
use App\Http\Resources\TransactionResource;

it('formats transaction resource correctly', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id]);

    $transaction = Transaction::factory()->create([
        'account_id' => $account->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'amount' => 50000,
        'transaction_date' => now(),
    ]);

    $resource = new TransactionResource($transaction);
    $array = $resource->resolve(); // using resolve() to fully resolve resources

    expect($array)
        ->toHaveKey('id', $transaction->id)
        ->toHaveKey('type', 'expense')
        ->toHaveKey('amount', 50000.0)
        ->toHaveKey('transaction_date', $transaction->transaction_date->format('Y-m-d'))
        ->not->toHaveKey('account') // because it wasn't loaded
        ->not->toHaveKey('category');
});

it('includes loaded relationships in transaction resource', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id]);

    $transaction = Transaction::factory()->create([
        'account_id' => $account->id,
        'category_id' => $category->id,
    ]);

    $transaction->load(['account', 'category']);

    $resource = new TransactionResource($transaction);
    $array = $resource->resolve();

    expect($array)
        ->toHaveKey('account')
        ->toHaveKey('category');

    expect($array['account']['id'])->toBe($account->id);
    expect($array['category']['id'])->toBe($category->id);
});
