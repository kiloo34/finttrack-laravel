<?php

namespace App\Modules\Transaction\Presentation\Http\Requests;

use App\Modules\Transaction\Domain\ValueObjects\ExecutionType;
use App\Modules\Transaction\Domain\ValueObjects\RecurringFrequency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecurringTransactionHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'account_id' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
            'destination_account_id' => ['nullable', 'integer', 'exists:accounts,id', 'different:account_id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'type' => ['sometimes', 'required', 'string', Rule::in(['income', 'expense', 'transfer'])],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'size:3'],
            'description' => ['nullable', 'string', 'max:255'],
            'frequency' => ['sometimes', 'required', 'string', Rule::in(array_column(RecurringFrequency::cases(), 'value'))],
            'execution_type' => ['sometimes', 'required', 'string', Rule::in(array_column(ExecutionType::cases(), 'value'))],
            'next_run_date' => ['sometimes', 'required', 'date'],
            'is_active' => ['boolean'],
        ];
    }
}
