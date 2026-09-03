<?php

namespace App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ledger entries are immutable — append-only, no updated_at.
 *
 * @property int $id
 * @property int $account_id
 * @property int|null $transaction_id
 * @property string $direction 'credit' or 'debit'
 * @property string $amount
 * @property string $currency
 * @property CarbonImmutable|null $created_at
 */
class LedgerEntryModel extends Model
{
    protected $table = 'ledger_entries';

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

    /** @return BelongsTo<AccountModel, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountModel::class, 'account_id');
    }

    /** @return BelongsTo<TransactionModel, $this> */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(TransactionModel::class, 'transaction_id');
    }
}
