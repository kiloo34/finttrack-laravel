<?php

namespace App\Domain\Account\Models;

use App\Domain\Ledger\Models\LedgerEntry;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\User\Models\User;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    protected static function newFactory(): AccountFactory
    {
        return AccountFactory::new();
    }

    /** @use HasFactory<AccountFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'currency',
        'initial_balance',
        'is_active',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Account $account) {
            if ($account->transactions()->exists()) {
                throw new \Exception('Account cannot be deleted because it has related transactions.');
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany<LedgerEntry, $this>
     */
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
