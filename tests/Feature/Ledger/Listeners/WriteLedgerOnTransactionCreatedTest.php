<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Ledger\Application\Listeners\WriteLedgerOnTransactionCreated;
use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Domain\Events\TransactionCreated;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates a ledger entry when transaction is completed', function () {
    $account = AccountModel::factory()->create();
    $transaction = TransactionModel::factory()->create(['account_id' => $account->id]);

    $event = new TransactionCreated(
        transactionId: $transaction->id,
        accountId: $account->id,
        amount: 250.00,
        direction: 'credit',
        currency: 'IDR',
        status: 'completed'
    );

    $listener = new WriteLedgerOnTransactionCreated;
    $listener->handle($event);

    expect(LedgerEntryModel::count())->toBe(1);

    $entry = LedgerEntryModel::first();
    expect($entry->transaction_id)->toBe($transaction->id)
        ->and($entry->account_id)->toBe($account->id)
        ->and($entry->direction)->toBe('credit')
        ->and((float) $entry->amount)->toBe(250.0)
        ->and($entry->currency)->toBe('IDR');
});

test('it does not create a ledger entry when transaction is not completed', function () {
    $account = AccountModel::factory()->create();
    $transaction = TransactionModel::factory()->create(['account_id' => $account->id]);

    $event = new TransactionCreated(
        transactionId: $transaction->id,
        accountId: $account->id,
        amount: 100.00,
        direction: 'debit',
        currency: 'IDR',
        status: 'pending'
    );

    $listener = new WriteLedgerOnTransactionCreated;
    $listener->handle($event);

    expect(LedgerEntryModel::count())->toBe(0);
});
