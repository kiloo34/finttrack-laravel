<?php

namespace App\Modules\Account\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderModel extends Model
{
    protected $table = 'providers';

    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * @return HasMany<AccountModel, $this>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(AccountModel::class, 'provider_id', 'id');
    }
}
