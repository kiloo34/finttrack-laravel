<?php

namespace App\Modules\Transaction\Presentation\Http\Requests;

use App\Modules\Transaction\Application\DTOs\UpdateTransactionInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', 'string', Rule::exists('transaction_types', 'value')],
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999999999'],
            'currency' => ['nullable', 'string', 'size:3'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(int $id): UpdateTransactionInput
    {
        return new UpdateTransactionInput(
            id: $id,
            accountId: (int) $this->input('account_id'),
            categoryId: $this->input('category_id') ? (int) $this->input('category_id') : null,
            type: $this->string('type')->toString(),
            amount: number_format((float) $this->input('amount'), 2, '.', ''),
            currency: $this->string('currency', 'IDR')->toString(),
            transactionDate: new \DateTimeImmutable($this->string('transaction_date')->toString()),
            description: $this->input('description'),
            reference: $this->input('reference'),
        );
    }
}
