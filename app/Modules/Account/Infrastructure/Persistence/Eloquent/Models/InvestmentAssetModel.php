<?php

namespace App\Modules\Account\Infrastructure\Persistence\Eloquent\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string|null $symbol
 * @property string $type
 * @property int|null $lots
 * @property string $units
 * @property string $average_buy_price
 * @property string $current_market_price
 * @property CarbonImmutable|null $last_synced_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class InvestmentAssetModel extends Model
{
    use SoftDeletes;

    protected $table = 'investment_assets';

    protected $fillable = [
        'account_id',
        'name',
        'symbol',
        'type',
        'lots',
        'units',
        'average_buy_price',
        'current_market_price',
        'last_synced_at',
    ];

    protected $casts = [
        'lots' => 'integer',
        'units' => 'decimal:8',
        'average_buy_price' => 'decimal:2',
        'current_market_price' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    /** @return BelongsTo<AccountModel, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountModel::class, 'account_id');
    }
}
