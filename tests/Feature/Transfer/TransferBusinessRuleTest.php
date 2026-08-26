<?php

use App\Domain\Account\Models\Account;
use App\Domain\Ledger\Models\LedgerEntry;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\User\Models\User;
use Illuminate\Validation\ValidationException;

// --- Transfer Business Rules Tests ---

test('transfer fails if from_account and to_account belong to different users', function () {
    $userA = User::factory()->create();
    $accountA = Account::factory()->create(['user_id' => $userA->id]);

    $userB = User::factory()->create();
    $accountB = Account::factory()->create(['user_id' => $userB->id]);

    expect(fn () => Transfer::factory()->create([
        'from_account_id' => $accountA->id,
        'to_account_id' => $accountB->id,
    ]))->toThrow(ValidationException::class);
});

test('transfer creates double ledger entries automatically', function () {
    $user = User::factory()->create();
    $fromAccount = Account::factory()->create(['user_id' => $user->id]);
    $toAccount = Account::factory()->create(['user_id' => $user->id]);

    // We don't use the factory to automatically trigger the logic if we put the logic in a Service,
    // but if we put the logic in the Model's booted method, it will trigger on create.
    $transfer = Transfer::factory()->create([
        'from_account_id' => $fromAccount->id,
        'to_account_id' => $toAccount->id,
        'amount' => 500,
    ]);

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

    // This might already be in TransferModelTest, but it's a good business rule to enforce
    // explicitly via mutator or validation.
    $transfer = Transfer::factory()->create([
        'from_account_id' => $fromAccount->id,
        'to_account_id' => $toAccount->id,
        'amount' => -100, // Negative amount
    ]);

    // Assuming we use a mutator to ensure positive amounts
    expect((float) $transfer->amount)->toBe(100.0);
});
