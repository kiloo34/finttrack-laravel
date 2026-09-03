<?php

namespace App\Modules\Category\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTypeModel extends Model
{
    protected $table = 'category_types';

    protected $primaryKey = 'value';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['value', 'label'];
}
