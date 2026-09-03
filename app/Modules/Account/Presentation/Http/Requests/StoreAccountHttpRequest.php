<?php

namespace App\Modules\Account\Presentation\Http\Requests;

use App\Modules\Account\Application\DTOs\CreateAccountInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountHttpRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::exists('account_types', 'value')],
            'currency' => ['nullable', 'string', 'size:3'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'provider_id' => ['nullable', 'integer', Rule::exists('providers', 'id')],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function toDto(): CreateAccountInput
    {
        /** @var int<1, max> $userId */
        $userId = auth()->id();

        return new CreateAccountInput(
            userId: $userId,
            name: $this->string('name')->toString(),
            type: $this->string('type')->toString(),
            currency: $this->string('currency', 'IDR')->toString(),
            accountNumber: $this->input('account_number') ? $this->string('account_number')->toString() : null,
            providerId: $this->input('provider_id') !== null ? (int) $this->input('provider_id') : null,
            initialBalance: number_format((float) $this->input('initial_balance', 0), 2, '.', ''),
            isActive: (bool) $this->input('is_active', true),
        );
    }
}
