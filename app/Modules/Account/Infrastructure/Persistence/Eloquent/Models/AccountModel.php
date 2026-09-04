<?php

namespace App\Modules\Account\Infrastructure\Persistence\Eloquent\Models;

use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Carbon\CarbonImmutable;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Eloquent persistence model — lives ONLY in Infrastructure.
 * Maps to the `accounts` table.
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $type
 * @property string $currency
 * @property string|null $account_number
 * @property int|null $provider_id
 * @property string $initial_balance
 * @property bool $is_active
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class AccountModel extends Model
{
    protected $table = 'accounts';

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
        'account_number',
        'provider_id',
        'initial_balance',
        'is_active',
        'sync_frequency',
        'deleted_reason',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function (AccountModel $account) {
            if ($account->transactions()->exists()) {
                throw new \Exception('Account cannot be deleted because it has related transactions.');
            }
        });
    }

    /** @return BelongsTo<UserModel, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /** @return HasMany<TransactionModel, $this> */
    public function transactions(): HasMany
    {
        return $this->hasMany(TransactionModel::class, 'account_id');
    }

    /** @return HasMany<InvestmentAssetModel, $this> */
    public function investmentAssets(): HasMany
    {
        return $this->hasMany(InvestmentAssetModel::class, 'account_id');
    }

    /** @return HasMany<LedgerEntryModel, $this> */
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntryModel::class, 'account_id');
    }

    /** @return BelongsTo<ProviderModel, $this> */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(ProviderModel::class, 'provider_id');
    }
}
