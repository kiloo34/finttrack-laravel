<?php

namespace App\Domain\Transaction\Models;

use App\Domain\Account\Models\Account;
use App\Domain\Category\Models\Category;
use App\Domain\Ledger\Models\LedgerEntry;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    protected static function newFactory(): TransactionFactory
    {
        return TransactionFactory::new();
    }

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'category_id',
        'type',
        'amount',
        'currency',
        'transaction_date',
        'description',
        'reference',
        'status',
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
        static::creating(function (Transaction $transaction) {
            if ($transaction->category_id) {
                // We load the relations if they are not already loaded
                $category = $transaction->category()->first();
                $account = $transaction->account()->first();
                
                if ($category && $account && ! $category->is_system && $category->user_id !== $account->user_id) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'category_id' => 'The category is invalid or belongs to another user.',
                    ]);
                }
            }
        });

        static::deleting(function (Transaction $transaction) {
            $transaction->ledgerEntries()->delete(); // soft delete related ledger entries
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
