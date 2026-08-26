<?php

use App\Domain\Account\Models\Account;
use App\Domain\Category\Models\Category;
use App\Domain\Ledger\Models\LedgerEntry;
use App\Domain\Transaction\Models\Transaction;

// --- Model & Relationship Tests ---

test('transaction belongs to an account', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->account)->toBeInstanceOf(Account::class);
});

test('transaction belongs to a category', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->category)->toBeInstanceOf(Category::class);
});

test('transaction can have no category', function () {
    $transaction = Transaction::factory()->create(['category_id' => null]);

    expect($transaction->category)->toBeNull();
});

test('account has many transactions', function () {
    $account = Account::factory()->create();
    Transaction::factory()->count(3)->create(['account_id' => $account->id]);

    expect($account->transactions)->toHaveCount(3);
});

// --- Amount Convention Tests ---

test('transaction amount is always stored as positive', function () {
    $transaction = Transaction::factory()->expense()->create(['amount' => 500000]);

    expect((float) $transaction->amount)->toBe(500000.00);
    expect((float) $transaction->amount)->toBeGreaterThan(0);
});

test('transaction type determines financial direction not sign of amount', function () {
    $income = Transaction::factory()->income()->create(['amount' => 1000000]);
    $expense = Transaction::factory()->expense()->create(['amount' => 500000]);

    // Both amounts are positive — type determines direction
    expect((float) $income->amount)->toBeGreaterThan(0);
    expect((float) $expense->amount)->toBeGreaterThan(0);
    expect($income->type)->toBe('income');
    expect($expense->type)->toBe('expense');
});

// --- Cast Tests ---

test('transaction amount is cast to decimal', function () {
    $transaction = Transaction::factory()->create(['amount' => 1234567.89]);

    expect((float) $transaction->amount)->toBe(1234567.89);
});

test('transaction_date is cast to date', function () {
    $transaction = Transaction::factory()->create(['transaction_date' => '2026-01-15']);

    expect($transaction->transaction_date)->toBeInstanceOf(DateTimeInterface::class);
    expect($transaction->transaction_date->format('Y-m-d'))->toBe('2026-01-15');
});

// --- Soft Delete Tests ---

test('transaction can be soft deleted', function () {
    $transaction = Transaction::factory()->create();
    $id = $transaction->id;

    $transaction->delete();

    expect(Transaction::find($id))->toBeNull();
    expect(Transaction::withTrashed()->find($id))->not->toBeNull();
});

// --- Ledger Entry Tests ---

test('ledger entry belongs to an account', function () {
    $transaction = Transaction::factory()->create();
    $entry = LedgerEntry::create([
        'account_id' => $transaction->account_id,
        'transaction_id' => $transaction->id,
        'direction' => 'credit',
        'amount' => $transaction->amount,
        'currency' => 'IDR',
    ]);

    expect($entry->account)->toBeInstanceOf(Account::class);
    expect($entry->transaction)->toBeInstanceOf(Transaction::class);
});

test('ledger entry has no updated_at timestamp', function () {
    $entry = new LedgerEntry;

    expect($entry->timestamps)->toBeFalse();
});

test('debit and credit balance for a transfer', function () {
    $fromAccount = Account::factory()->create();
    $toAccount = Account::factory()->create();
    $amount = 500000.00;

    // Simulate ledger entries for a transfer
    $debit = LedgerEntry::create([
        'account_id' => $fromAccount->id,
        'transaction_id' => null,
        'direction' => 'debit',
        'amount' => $amount,
        'currency' => 'IDR',
    ]);

    $credit = LedgerEntry::create([
        'account_id' => $toAccount->id,
        'transaction_id' => null,
        'direction' => 'credit',
        'amount' => $amount,
        'currency' => 'IDR',
    ]);

    // Financial invariant: total debit == total credit
    expect((float) $debit->amount)->toBe((float) $credit->amount);
    expect($debit->direction)->toBe('debit');
    expect($credit->direction)->toBe('credit');
});
