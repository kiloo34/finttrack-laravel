<?php

namespace App\Modules\Transfer\Presentation\Http\Requests;

use App\Modules\Transfer\Application\DTOs\CreateTransferInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransferHttpRequest extends FormRequest
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
            'from_account_id' => ['required', 'exists:accounts,id'],
            'to_account_id' => ['required', 'exists:accounts,id', 'different:from_account_id'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999999999'],
            'currency' => ['required', 'string', 'size:3'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): CreateTransferInput
    {
        return new CreateTransferInput(
            fromAccountId: (int) $this->input('from_account_id'),
            toAccountId: (int) $this->input('to_account_id'),
            amount: number_format((float) $this->input('amount'), 2, '.', ''),
            currency: $this->string('currency')->toString(),
            transactionDate: new \DateTimeImmutable($this->string('transaction_date')->toString()),
            description: $this->input('description'),
        );
    }
}
