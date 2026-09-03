<?php

namespace App\Modules\Account\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTypeModel extends Model
{
    protected $table = 'account_types';

    protected $primaryKey = 'value';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['value', 'label'];
}
