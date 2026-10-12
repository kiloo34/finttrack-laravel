<?php

namespace App\Modules\Budget\Infrastructure\Persistence\Eloquent\Models;

use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $amount
 * @property int $month
 * @property int $year
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 */
class BudgetModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'budgets';

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'month',
        'year',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer',
    ];

    /**
     * @return BelongsTo<UserModel, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /**
     * @return BelongsTo<CategoryModel, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }
}

