<?php

use App\Modules\Account\Application\UseCases\SyncAccountBalanceUseCase;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->user = UserModel::factory()->create();
    $this->account = AccountModel::factory()->create([
        'user_id' => $this->user->id,
        'initial_balance' => '1000.00',
    ]);

    $this->expenseCategory = CategoryModel::factory()->create([
        'type' => 'expense',
        'user_id' => $this->user->id,
    ]);
    $this->incomeCategory = CategoryModel::factory()->create([
        'type' => 'income',
        'user_id' => $this->user->id,
    ]);

    $this->useCase = app(SyncAccountBalanceUseCase::class);
});

test('it throws validation exception if synced outside allowed dates', function () {
    // 15th is outside the 25-5 range
    $invalidDate = Carbon::create(2023, 1, 15);

    expect(fn () => $this->useCase->execute(
        $this->account->id,
        '1000.00',
        $this->expenseCategory->id,
        $invalidDate
    ))->toThrow(ValidationException::class, 'Balance sync is only allowed between the 25th and the 5th of the month.');
});

test('it allows syncing on the 25th', function () {
    $validDate = Carbon::create(2023, 1, 25);

    // Exact match, no transaction created
    $this->useCase->execute($this->account->id, '1000.00', $this->expenseCategory->id, $validDate);

    expect(TransactionModel::count())->toBe(0);
});

test('it allows syncing on the 5th', function () {
    $validDate = Carbon::create(2023, 1, 5);

    $this->useCase->execute($this->account->id, '1000.00', $this->expenseCategory->id, $validDate);

    expect(TransactionModel::count())->toBe(0);
});

test('it creates an expense transaction if actual balance is lower than system balance', function () {
    $validDate = Carbon::create(2023, 1, 28);

    // System is 1000.00, actual is 950.00 -> Missing 50.00
    $this->useCase->execute($this->account->id, '950.00', $this->expenseCategory->id, $validDate);

    $transaction = TransactionModel::first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->account_id)->toBe($this->account->id)
        ->and($transaction->category_id)->toBe($this->expenseCategory->id)
        ->and($transaction->type)->toBe('expense')
        ->and((float) $transaction->amount)->toBe(50.00);
});

test('it creates an income transaction if actual balance is higher than system balance', function () {
    $validDate = Carbon::create(2023, 1, 1);

    // System is 1000.00, actual is 1050.00 -> Extra 50.00
    $this->useCase->execute($this->account->id, '1050.00', $this->incomeCategory->id, $validDate);

    $transaction = TransactionModel::first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->account_id)->toBe($this->account->id)
        ->and($transaction->category_id)->toBe($this->incomeCategory->id)
        ->and($transaction->type)->toBe('income')
        ->and((float) $transaction->amount)->toBe(50.00);
});

test('it accurately calculates discrepancy when ledger entries exist', function () {
    $validDate = Carbon::create(2023, 1, 30);

    // Add an income of 500.00 (system balance should be 1500.00)
    $trx = TransactionModel::factory()->create([
        'account_id' => $this->account->id,
        'type' => 'income',
        'amount' => '500.00',
        'status' => 'completed',
        'transaction_date' => $validDate->copy()->subDay(),
    ]);

    LedgerEntryModel::create([
        'account_id' => $this->account->id,
        'transaction_id' => $trx->id,
        'direction' => 'credit',
        'amount' => '500.00',
        'currency' => 'IDR',
        'created_at' => $validDate->copy()->subDay(),
        'updated_at' => $validDate->copy()->subDay(),
    ]);

    // Actual balance is 1200.00. (Discrepancy is -300.00 -> expense)
    $this->useCase->execute($this->account->id, '1200.00', $this->expenseCategory->id, $validDate);

    // Note: The factory created 1 transaction, the sync creates 1 more. Total = 2.
    expect(TransactionModel::count())->toBe(2);

    $syncTransaction = TransactionModel::where('description', 'like', 'Balance Adjustment%')->first();

    expect($syncTransaction)->not->toBeNull()
        ->and($syncTransaction->type)->toBe('expense')
        ->and((float) $syncTransaction->amount)->toBe(300.00);
});
