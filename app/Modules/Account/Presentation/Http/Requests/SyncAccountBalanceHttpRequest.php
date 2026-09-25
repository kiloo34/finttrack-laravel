<?php

namespace App\Modules\Account\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SyncAccountBalanceHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // We check ownership in the controller
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'actual_balance' => ['required', 'numeric'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }
}
