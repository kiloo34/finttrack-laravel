<?php

namespace App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $account_id
 * @property int|null $destination_account_id
 * @property int|null $category_id
 * @property string $type
 * @property string $amount
 * @property string $currency
 * @property string|null $description
 * @property string $frequency
 * @property string $execution_type
 * @property CarbonImmutable $next_run_date
 * @property bool $is_active
 */
class RecurringTransactionModel extends Model
{
    use SoftDeletes;

    protected $table = 'recurring_transactions';

    protected $fillable = [
        'user_id',
        'account_id',
        'destination_account_id',
        'category_id',
        'type',
        'amount',
        'currency',
        'description',
        'frequency',
        'execution_type',
        'next_run_date',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'next_run_date' => 'date',
        'is_active' => 'boolean',
    ];

    /** @return BelongsTo<AccountModel, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountModel::class, 'account_id');
    }

    /** @return BelongsTo<AccountModel, $this> */
    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(AccountModel::class, 'destination_account_id');
    }

    /** @return BelongsTo<CategoryModel, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }
}
