<?php

namespace App\Modules\Transfer\Infrastructure\Persistence\Eloquent\Models;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use Carbon\CarbonImmutable;
use Database\Factories\TransferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $from_account_id
 * @property int $to_account_id
 * @property string $amount
 * @property string $currency
 * @property CarbonImmutable $transaction_date
 * @property string|null $description
 */
class TransferModel extends Model
{
    protected $table = 'transfers';

    protected static function newFactory(): TransferFactory
    {
        return TransferFactory::new();
    }

    /** @use HasFactory<TransferFactory> */
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

    /** @return BelongsTo<AccountModel, $this> */
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(AccountModel::class, 'from_account_id');
    }

    /** @return BelongsTo<AccountModel, $this> */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(AccountModel::class, 'to_account_id');
    }
}
