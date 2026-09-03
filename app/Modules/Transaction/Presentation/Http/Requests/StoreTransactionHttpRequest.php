<?php

namespace App\Modules\Transaction\Presentation\Http\Requests;

use App\Modules\Transaction\Application\DTOs\CreateTransactionInput;
use App\Modules\Transaction\Domain\ValueObjects\TransactionStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionHttpRequest extends FormRequest
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
            'status' => ['nullable', 'string', Rule::enum(TransactionStatus::class)],
        ];
    }

    public function toDto(): CreateTransactionInput
    {
        return new CreateTransactionInput(
            accountId: (int) $this->input('account_id'),
            categoryId: $this->input('category_id') ? (int) $this->input('category_id') : null,
            type: $this->string('type')->toString(),
            amount: number_format((float) $this->input('amount'), 2, '.', ''),
            currency: $this->string('currency', 'IDR')->toString(),
            transactionDate: new \DateTimeImmutable($this->string('transaction_date')->toString()),
            description: $this->input('description'),
            reference: $this->input('reference'),
            status: $this->string('status', 'completed')->toString(),
        );
    }
}
