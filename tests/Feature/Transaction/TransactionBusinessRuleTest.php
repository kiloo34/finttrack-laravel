<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel as Account;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel as Category;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel as Transaction;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;
use Illuminate\Validation\ValidationException;

// --- Transaction Business Rules Tests ---

test('transaction cannot use a category belonging to another user', function () {
    $userA = User::factory()->create();
    $accountA = Account::factory()->create(['user_id' => $userA->id]);

    $userB = User::factory()->create();
    $categoryB = Category::factory()->create(['user_id' => $userB->id]);

    // Should throw an exception when trying to use User B's category for User A's account
    expect(fn () => Transaction::factory()->create([
        'account_id' => $accountA->id,
        'category_id' => $categoryB->id,
    ]))->toThrow(ValidationException::class);
});

test('transaction can use a system category', function () {
    $userA = User::factory()->create();
    $accountA = Account::factory()->create(['user_id' => $userA->id]);

    $systemCategory = Category::factory()->system()->create();

    $transaction = Transaction::factory()->create([
        'account_id' => $accountA->id,
        'category_id' => $systemCategory->id,
    ]);

    expect($transaction->id)->not->toBeNull();
});

test('transaction soft delete cascades to ledger entries', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    $transaction = Transaction::factory()->create([
        'account_id' => $account->id,
    ]);

    // A LedgerEntry is automatically created by the factory state (or service)
    // Wait, our factory doesn't automatically create a ledger entry right now.
    // Let's create one manually to simulate what the Service would do.
    $ledgerEntry = $transaction->ledgerEntries()->create([
        'account_id' => $account->id,
        'direction' => 'debit',
        'amount' => 100,
        'description' => 'test',
    ]);

    // Soft delete the transaction
    $transaction->delete();

    // The transaction should be soft deleted
    expect($transaction->trashed())->toBeTrue();

    // The ledger entry should be hard deleted (since it doesn't use SoftDeletes)
    expect($ledgerEntry->fresh())->toBeNull();
});
