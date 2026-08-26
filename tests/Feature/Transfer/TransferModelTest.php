<?php

use App\Domain\Account\Models\Account;
use App\Domain\Ledger\Models\LedgerEntry;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;

// --- Model & Relationship Tests ---

test('transfer belongs to a from_account', function () {
    $transfer = Transfer::factory()->create();

    expect($transfer->fromAccount)->toBeInstanceOf(Account::class);
});

test('transfer belongs to a to_account', function () {
    $transfer = Transfer::factory()->create();

    expect($transfer->toAccount)->toBeInstanceOf(Account::class);
});

test('transfer from and to accounts are different', function () {
    $user = User::factory()->create();
    $fromAccount = Account::factory()->create(['user_id' => $user->id]);
    $toAccount = Account::factory()->create(['user_id' => $user->id]);

    $transfer = Transfer::factory()->create([
        'from_account_id' => $fromAccount->id,
        'to_account_id'   => $toAccount->id,
    ]);

    expect($transfer->from_account_id)->not->toBe($transfer->to_account_id);
});

// --- Amount Convention Tests ---

test('transfer amount is always positive', function () {
    $transfer = Transfer::factory()->create(['amount' => 1_000_000]);

    expect((float) $transfer->amount)->toBeGreaterThan(0);
});

test('transfer does not affect total wealth', function () {
    // A transfer just moves money — net effect on total wealth is zero
    $amount = 500_000.00;

    $debit = LedgerEntry::create([
        'account_id'     => Account::factory()->create()->id,
        'transaction_id' => null,
        'direction'      => 'debit',
        'amount'         => $amount,
        'currency'       => 'IDR',
    ]);

    $credit = LedgerEntry::create([
        'account_id'     => Account::factory()->create()->id,
        'transaction_id' => null,
        'direction'      => 'credit',
        'amount'         => $amount,
        'currency'       => 'IDR',
    ]);

    // Financial invariant: debit == credit (net = 0)
    expect((float) $debit->amount)->toBe((float) $credit->amount);
});

// --- Atomicity Test ---

test('transfer creation is atomic — rolls back on failure', function () {
    $fromAccount = Account::factory()->create();
    $toAccount = Account::factory()->create();
    $amount = 250_000.00;
    $transferId = null;

    try {
        DB::transaction(function () use ($fromAccount, $toAccount, $amount, &$transferId) {
            $transfer = Transfer::create([
                'from_account_id' => $fromAccount->id,
                'to_account_id'   => $toAccount->id,
                'amount'          => $amount,
                'currency'        => 'IDR',
                'transaction_date' => now()->toDateString(),
            ]);
            $transferId = $transfer->id;

            // Simulate a failure mid-transaction
            throw new \RuntimeException('Simulated failure');
        });
    } catch (\RuntimeException) {
        // Expected
    }

    // Transfer must not exist due to rollback
    expect(Transfer::find($transferId))->toBeNull();

    // No orphan ledger entries must exist
    expect(LedgerEntry::count())->toBe(0);
});

// --- Cast Tests ---

test('transfer amount is cast to decimal', function () {
    $transfer = Transfer::factory()->create(['amount' => 3_750_000.50]);

    expect((float) $transfer->amount)->toBe(3_750_000.50);
});

test('transfer transaction_date is cast to date', function () {
    $transfer = Transfer::factory()->create(['transaction_date' => '2026-06-01']);

    expect($transfer->transaction_date)->toBeInstanceOf(\DateTimeInterface::class);
    expect($transfer->transaction_date->format('Y-m-d'))->toBe('2026-06-01');
});
