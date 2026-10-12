<?php

namespace App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use Carbon\CarbonImmutable;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $account_id
 * @property int|null $category_id
 * @property string $type
 * @property string $amount
 * @property string $currency
 * @property CarbonImmutable $transaction_date
 * @property string|null $description
 * @property string|null $reference
 * @property string $status
 * @property string|null $attachment_url
 */
class TransactionModel extends Model
{
    protected $table = 'transactions';

    protected static function newFactory(): TransactionFactory
    {
        return TransactionFactory::new();
    }

    /** @use HasFactory<TransactionFactory> */
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
        'attachment_url',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (TransactionModel $transaction) {
            if ($transaction->category_id) {
                /** @var CategoryModel|null $category */
                $category = $transaction->category()->first();
                /** @var AccountModel|null $account */
                $account = $transaction->account()->first();

                if ($category && $account && ! $category->is_system && $category->user_id !== $account->user_id) {
                    throw ValidationException::withMessages([
                        'category_id' => 'The category is invalid or belongs to another user.',
                    ]);
                }
            }
        });

        static::deleting(function (TransactionModel $transaction) {
            $transaction->ledgerEntries()->delete();
        });
    }

    /** @return BelongsTo<AccountModel, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountModel::class, 'account_id');
    }

    /** @return BelongsTo<CategoryModel, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    /** @return HasMany<LedgerEntryModel, $this> */
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntryModel::class, 'transaction_id');
    }
}
