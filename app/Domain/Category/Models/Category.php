<?php

namespace App\Domain\Category\Models;

use App\Models\User;
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
