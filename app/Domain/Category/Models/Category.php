<?php

namespace App\Domain\Category\Models;

use App\Domain\User\Models\User;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::updating(function (Category $category) {
            if ($category->getOriginal('is_system') === true) {
                throw new \Exception('System categories cannot be modified.');
            }
        });

        static::deleting(function (Category $category) {
            if ($category->is_system) {
                throw new \Exception('System categories cannot be deleted.');
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: user-owned categories AND system categories.
     */
    public function scopeAvailableFor(\Illuminate\Database\Eloquent\Builder $query, int $userId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_system', true);
        });
    }

    /**
     * Scope: only user-owned categories (excluding system categories).
     */
    public function scopeForUser(\Illuminate\Database\Eloquent\Builder $query, int $userId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: only system categories (shared across all users).
     */
    public function scopeSystem(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNull('user_id')->where('is_system', true);
    }
}
