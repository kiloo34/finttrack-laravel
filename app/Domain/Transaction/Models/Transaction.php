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
