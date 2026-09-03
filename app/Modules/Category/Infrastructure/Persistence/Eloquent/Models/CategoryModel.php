<?php

namespace App\Modules\Category\Infrastructure\Persistence\Eloquent\Models;

use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Carbon\CarbonImmutable;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $parent_id
 * @property string $name
 * @property string $type
 * @property bool $is_system
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class CategoryModel extends Model
{
    protected $table = 'categories';

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'type',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /** @return BelongsTo<UserModel, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /** @return BelongsTo<CategoryModel, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CategoryModel::class, 'parent_id');
    }

    /** @return HasMany<CategoryModel, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(CategoryModel::class, 'parent_id');
    }

    /**
     * Scope: user-owned AND system categories.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeAvailableFor(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('is_system', true);
        });
    }

    /**
     * @param  Builder<CategoryModel>  $query
     * @return Builder<CategoryModel>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * @param  Builder<CategoryModel>  $query
     * @return Builder<CategoryModel>
     */
    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('is_system', true);
    }
}
