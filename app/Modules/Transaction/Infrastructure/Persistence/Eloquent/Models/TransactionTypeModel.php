<?php

namespace App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionTypeModel extends Model
{
    protected $table = 'transaction_types';

    protected $primaryKey = 'value';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['value', 'label'];
}
