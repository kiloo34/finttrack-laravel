<?php

namespace App\Domain\Transfer\Models;

use App\Domain\Account\Models\Account;
use App\Domain\Ledger\Models\LedgerEntry;
use Database\Factories\TransferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    protected static function newFactory(): TransferFactory
    {
        return TransferFactory::new();
    }

    /** @use HasFactory<\Database\Factories\TransferFactory> */
    use HasFactory;

    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'currency',
        'transaction_date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Transfer $transfer) {
            // Ensure amount is always positive
            $transfer->amount = abs((float) $transfer->amount);

            // Ensure from_account and to_account belong to the same user
            /** @var Account|null $fromAccount */
            $fromAccount = $transfer->fromAccount()->first();
            /** @var Account|null $toAccount */
            $toAccount = $transfer->toAccount()->first();

            if ($fromAccount && $toAccount && $fromAccount->user_id !== $toAccount->user_id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'to_account_id' => 'Transfer can only be made between accounts belonging to the same user.',
                ]);
            }
        });

        static::created(function (Transfer $transfer) {
            // Create Debit entry for from_account
            LedgerEntry::create([
                'account_id' => $transfer->from_account_id,
                'direction' => 'debit',
                'amount' => $transfer->amount,
                'currency' => $transfer->currency,
            ]);

            // Create Credit entry for to_account
            LedgerEntry::create([
                'account_id' => $transfer->to_account_id,
                'direction' => 'credit',
                'amount' => $transfer->amount,
                'currency' => $transfer->currency,
            ]);
        });
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
}
