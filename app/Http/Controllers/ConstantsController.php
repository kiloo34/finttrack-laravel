<?php

namespace App\Http\Controllers;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountTypeModel;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\CurrencyModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryTypeModel;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionTypeModel;
use Illuminate\Http\JsonResponse;

class ConstantsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'ACCOUNT_TYPES' => AccountTypeModel::all(),
            'CATEGORY_TYPES' => CategoryTypeModel::all(),
            'CURRENCIES' => CurrencyModel::all(),
            'TRANSACTION_TYPES' => TransactionTypeModel::all(),
        ]);
    }
}
