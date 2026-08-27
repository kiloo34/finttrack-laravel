<?php

namespace App\Domain\Ledger\Models;

use App\Domain\Account\Models\Account;
use App\Domain\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    /**
     * Ledger entries are immutable — no updated_at timestamp.
     */
    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'transaction_id',
        'direction',
        'amount',
        'currency',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo<Transaction, $this>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
