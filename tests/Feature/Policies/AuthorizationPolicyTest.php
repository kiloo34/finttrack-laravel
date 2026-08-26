<?php

use App\Domain\Account\Models\Account;
use App\Domain\Category\Models\Category;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transfer\Models\Transfer;
use App\Models\User;
use App\Policies\AccountPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\TransferPolicy;

// ============================================================
// AccountPolicy
// ============================================================

test('user can view their own account', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    expect((new AccountPolicy())->view($user, $account))->toBeTrue();
});

test('user cannot view another users account', function () {
    $user = User::factory()->create();
    $otherAccount = Account::factory()->create(); // belongs to a different user

    expect((new AccountPolicy())->view($user, $otherAccount))->toBeFalse();
});

test('user can update their own account', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    expect((new AccountPolicy())->update($user, $account))->toBeTrue();
});

test('user cannot update another users account', function () {
    $user = User::factory()->create();
    $otherAccount = Account::factory()->create();

    expect((new AccountPolicy())->update($user, $otherAccount))->toBeFalse();
});

test('user can delete their own account', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    expect((new AccountPolicy())->delete($user, $account))->toBeTrue();
});

test('user cannot delete another users account', function () {
    $user = User::factory()->create();
    $otherAccount = Account::factory()->create();

    expect((new AccountPolicy())->delete($user, $otherAccount))->toBeFalse();
});

// ============================================================
// CategoryPolicy
// ============================================================

test('user can view their own category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    expect((new CategoryPolicy())->view($user, $category))->toBeTrue();
});

test('user can view a system category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->system()->create();

    expect((new CategoryPolicy())->view($user, $category))->toBeTrue();
});

test('user cannot update a system category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->system()->create();

    expect((new CategoryPolicy())->update($user, $category))->toBeFalse();
});

test('user can update their own category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    expect((new CategoryPolicy())->update($user, $category))->toBeTrue();
});

test('user cannot update another users category', function () {
    $user = User::factory()->create();
    $otherCategory = Category::factory()->create();

    expect((new CategoryPolicy())->update($user, $otherCategory))->toBeFalse();
});

// ============================================================
// TransactionPolicy
// ============================================================

test('user can view transaction on their own account', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $transaction = Transaction::factory()->create(['account_id' => $account->id]);

    expect((new TransactionPolicy())->view($user, $transaction))->toBeTrue();
});

test('user cannot view transaction on another users account', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create(); // belongs to another user's account

    expect((new TransactionPolicy())->view($user, $transaction))->toBeFalse();
});

// ============================================================
// TransferPolicy
// ============================================================

test('user can view transfer where they own the from_account', function () {
    $user = User::factory()->create();
    $fromAccount = Account::factory()->create(['user_id' => $user->id]);
    $transfer = Transfer::factory()->create(['from_account_id' => $fromAccount->id]);

    expect((new TransferPolicy())->view($user, $transfer))->toBeTrue();
});

test('user cannot view transfer involving another users accounts', function () {
    $user = User::factory()->create();
    $transfer = Transfer::factory()->create(); // belongs to other user's accounts

    expect((new TransferPolicy())->view($user, $transfer))->toBeFalse();
});
