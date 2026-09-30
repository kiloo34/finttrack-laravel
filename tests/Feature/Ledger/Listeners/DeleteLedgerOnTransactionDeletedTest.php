<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Ledger\Application\Listeners\DeleteLedgerOnTransactionDeleted;
use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Domain\Events\TransactionDeleted;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it deletes ledger entries associated with the transaction', function () {
    $account = AccountModel::factory()->create();
    $transaction = TransactionModel::factory()->create(['account_id' => $account->id]);

    LedgerEntryModel::create([
        'account_id' => $account->id,
        'transaction_id' => $transaction->id,
        'direction' => 'credit',
        'amount' => 200,
        'currency' => 'EUR',
    ]);

    expect(LedgerEntryModel::count())->toBe(1);

    $event = new TransactionDeleted(transactionId: $transaction->id, accountId: $account->id);

    $listener = new DeleteLedgerOnTransactionDeleted;
    $listener->handle($event);

    expect(LedgerEntryModel::count())->toBe(0);
});
