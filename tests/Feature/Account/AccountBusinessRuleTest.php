<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel as Account;
use App\Modules\Account\Infrastructure\Services\AccountBalanceService;
use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel as LedgerEntry;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel as Transaction;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;
use Illuminate\Support\Carbon;

// --- Account Business Rules Tests ---

test('account cannot be deleted if it has transactions', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    // Create a transaction for this account
    Transaction::factory()->create(['account_id' => $account->id]);

    // Attempting to delete the account should throw an exception
    expect(fn () => $account->delete())->toThrow(Exception::class, 'Account cannot be deleted because it has related transactions.');

    // Account should still exist
    expect($account->fresh())->not->toBeNull();
});

test('account balance service calculates historical balance correctly', function () {
    $user = User::factory()->create();
    // Initial balance is 0 for testing simplicity
    $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 0]);

    // Simulate transactions in the past
    // Note: Since LedgerEntry is immutable and uses created_at, we will manually set created_at
    LedgerEntry::insert([
        [
            'account_id' => $account->id,
            'direction' => 'credit',
            'amount' => 1000,
            'currency' => 'IDR',
            'created_at' => Carbon::now()->subDays(10)->toDateTimeString(),
        ],
        [
            'account_id' => $account->id,
            'direction' => 'debit',
            'amount' => 200,
            'currency' => 'IDR',
            'created_at' => Carbon::now()->subDays(5)->toDateTimeString(),
        ],
        [
            'account_id' => $account->id,
            'direction' => 'credit',
            'amount' => 500,
            'currency' => 'IDR',
            'created_at' => Carbon::now()->toDateTimeString(), // Today
        ],
    ]);

    $service = new AccountBalanceService;

    // Balance 7 days ago: Only the 1000 credit should count. (1000)
    $historicalDate1 = Carbon::now()->subDays(7);
    expect($service->calculateFromModel($account, $historicalDate1))->toBe('1000.00');

    // Balance 2 days ago: The 1000 credit and 200 debit should count. (800)
    $historicalDate2 = Carbon::now()->subDays(2);
    expect($service->calculateFromModel($account, $historicalDate2))->toBe('800.00');

    // Current balance: All should count. (1300)
    expect($service->calculateFromModel($account))->toBe('1300.00');
});
