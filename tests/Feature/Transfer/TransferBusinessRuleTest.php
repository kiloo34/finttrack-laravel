<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel as Account;
use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel as LedgerEntry;
use App\Modules\Transfer\Application\DTOs\CreateTransferInput;
use App\Modules\Transfer\Application\UseCases\CreateTransferUseCase;
use App\Modules\Transfer\Infrastructure\Persistence\Eloquent\Models\TransferModel as Transfer;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;
use Illuminate\Validation\ValidationException;

// --- Transfer Business Rules Tests ---

test('transfer fails if from_account and to_account belong to different users', function () {
    $userA = User::factory()->create();
    $accountA = Account::factory()->create(['user_id' => $userA->id]);

    $userB = User::factory()->create();
    $accountB = Account::factory()->create(['user_id' => $userB->id]);

    $useCase = app(CreateTransferUseCase::class);

    expect(fn () => $useCase->execute(new CreateTransferInput(
        fromAccountId: $accountA->id,
        toAccountId: $accountB->id,
        amount: '100',
        currency: 'IDR',
        transactionDate: new DateTimeImmutable,
        description: null
    ), $userA->id))->toThrow(ValidationException::class);
});

test('transfer creates double ledger entries automatically', function () {
    $user = User::factory()->create();
    $fromAccount = Account::factory()->create(['user_id' => $user->id]);
    $toAccount = Account::factory()->create(['user_id' => $user->id]);

    $useCase = app(CreateTransferUseCase::class);
    $useCase->execute(new CreateTransferInput(
        fromAccountId: $fromAccount->id,
        toAccountId: $toAccount->id,
        amount: '500',
        currency: 'IDR',
        transactionDate: new DateTimeImmutable,
        description: null
    ), $user->id);

    // Check from_account ledger entry (Debit)
    $debitEntry = LedgerEntry::where('account_id', $fromAccount->id)
        ->where('amount', 500)
        ->where('direction', 'debit')
        ->first();
    expect($debitEntry)->not->toBeNull();

    // Check to_account ledger entry (Credit)
    $creditEntry = LedgerEntry::where('account_id', $toAccount->id)
        ->where('amount', 500)
        ->where('direction', 'credit')
        ->first();
    expect($creditEntry)->not->toBeNull();
});

test('transfer amount is always positive', function () {
    $user = User::factory()->create();
    $fromAccount = Account::factory()->create(['user_id' => $user->id]);
    $toAccount = Account::factory()->create(['user_id' => $user->id]);

    $useCase = app(CreateTransferUseCase::class);
    $output = $useCase->execute(new CreateTransferInput(
        fromAccountId: $fromAccount->id,
        toAccountId: $toAccount->id,
        amount: '-100',
        currency: 'IDR',
        transactionDate: new DateTimeImmutable,
        description: null
    ), $user->id);

    // Assuming we use a mutator or the entity ensures positive amounts
    expect((float) $output->amount)->toBe(100.0);
});
