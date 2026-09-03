<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Ledger\Application\Listeners\SyncLedgerOnTransactionUpdated;
use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Domain\Events\TransactionUpdated;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates a new ledger entry if completed and not exists', function () {
    $account = AccountModel::factory()->create();
    $transaction = TransactionModel::factory()->create(['account_id' => $account->id]);

    $event = new TransactionUpdated(
        transactionId: $transaction->id,
        accountId: $account->id,
        amount: 500.00,
        direction: 'debit',
        currency: 'USD',
        status: 'completed'
    );

    $listener = new SyncLedgerOnTransactionUpdated;
    $listener->handle($event);

    expect(LedgerEntryModel::count())->toBe(1);
    $entry = LedgerEntryModel::first();
    expect($entry->transaction_id)->toBe($transaction->id);
});

test('it updates existing ledger entry if completed and already exists', function () {
    $account = AccountModel::factory()->create();
    $transaction = TransactionModel::factory()->create(['account_id' => $account->id]);

    LedgerEntryModel::create([
        'account_id' => $account->id,
        'transaction_id' => $transaction->id,
        'direction' => 'debit',
        'amount' => 100,
        'currency' => 'USD',
    ]);

    $event = new TransactionUpdated(
        transactionId: $transaction->id,
        accountId: $account->id,
        amount: 350.00, // Amount changed
        direction: 'debit',
        currency: 'USD',
        status: 'completed'
    );

    $listener = new SyncLedgerOnTransactionUpdated;
    $listener->handle($event);

    expect(LedgerEntryModel::count())->toBe(1);
    $entry = LedgerEntryModel::where('transaction_id', $transaction->id)->first();
    expect((float) $entry->amount)->toBe(350.0);
});

test('it deletes existing ledger entry if status changed to pending or cancelled', function () {
    $account = AccountModel::factory()->create();
    $transaction = TransactionModel::factory()->create(['account_id' => $account->id]);

    LedgerEntryModel::create([
        'account_id' => $account->id,
        'transaction_id' => $transaction->id,
        'direction' => 'debit',
        'amount' => 100,
        'currency' => 'USD',
    ]);

    $event = new TransactionUpdated(
        transactionId: $transaction->id,
        accountId: $account->id,
        amount: 100.00,
        direction: 'debit',
        currency: 'USD',
        status: 'cancelled' // Status changed
    );

    $listener = new SyncLedgerOnTransactionUpdated;
    $listener->handle($event);

    expect(LedgerEntryModel::count())->toBe(0);
});
