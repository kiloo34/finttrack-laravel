<?php

use App\Domain\Account\Models\Account;
use App\Domain\Account\Services\AccountBalanceService;
use App\Domain\Ledger\Models\LedgerEntry;
use App\Models\User;

// --- Balance Calculation Tests ---

test('balance equals initial_balance when there are no ledger entries', function () {
    $account = Account::factory()->create(['initial_balance' => 1_000_000.00]);

    $service = new AccountBalanceService();

    expect($service->calculate($account))->toBe('1000000.00');
});

test('balance increases with credit entries', function () {
    $account = Account::factory()->create(['initial_balance' => 0]);

    LedgerEntry::create([
        'account_id'     => $account->id,
        'transaction_id' => null,
        'direction'      => 'credit',
        'amount'         => 500_000.00,
        'currency'       => 'IDR',
    ]);

    $service = new AccountBalanceService();

    expect($service->calculate($account))->toBe('500000.00');
});

test('balance decreases with debit entries', function () {
    $account = Account::factory()->create(['initial_balance' => 1_000_000.00]);

    LedgerEntry::create([
        'account_id'     => $account->id,
        'transaction_id' => null,
        'direction'      => 'debit',
        'amount'         => 300_000.00,
        'currency'       => 'IDR',
    ]);

    $service = new AccountBalanceService();

    expect($service->calculate($account))->toBe('700000.00');
});

test('balance formula: initial + credits - debits', function () {
    $account = Account::factory()->create(['initial_balance' => 2_000_000.00]);

    // +1,500,000 credit
    LedgerEntry::create([
        'account_id' => $account->id, 'transaction_id' => null,
        'direction' => 'credit', 'amount' => 1_500_000.00, 'currency' => 'IDR',
    ]);

    // -500,000 debit
    LedgerEntry::create([
        'account_id' => $account->id, 'transaction_id' => null,
        'direction' => 'debit', 'amount' => 500_000.00, 'currency' => 'IDR',
    ]);

    // Expected: 2,000,000 + 1,500,000 - 500,000 = 3,000,000
    $service = new AccountBalanceService();
    expect($service->calculate($account))->toBe('3000000.00');
});

test('balance uses precise decimal arithmetic (no floating point errors)', function () {
    $account = Account::factory()->create(['initial_balance' => 0]);

    // 0.1 + 0.2 is the classic floating point trap
    LedgerEntry::create([
        'account_id' => $account->id, 'transaction_id' => null,
        'direction' => 'credit', 'amount' => '0.10', 'currency' => 'IDR',
    ]);

    LedgerEntry::create([
        'account_id' => $account->id, 'transaction_id' => null,
        'direction' => 'credit', 'amount' => '0.20', 'currency' => 'IDR',
    ]);

    $service = new AccountBalanceService();

    // Must be exactly 0.30, not 0.30000000000000004
    expect($service->calculate($account))->toBe('0.30');
});

test('totalForUser sums balance across all active accounts', function () {
    $user = User::factory()->create();

    $account1 = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 1_000_000]);
    $account2 = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 2_000_000]);
    // Inactive account — should NOT be counted
    Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 9_000_000, 'is_active' => false]);

    $service = new AccountBalanceService();

    expect($service->totalForUser($user->id))->toBe('3000000.00');
});
