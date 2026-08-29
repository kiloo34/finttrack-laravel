<?php

namespace App\Modules\Account\Presentation\Http\Requests;

use App\Modules\Account\Application\DTOs\UpdateAccountInput;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountHttpRequest extends FormRequest
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
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function toDto(string $accountId): UpdateAccountInput
    {
        return new UpdateAccountInput(
            id: AccountId::fromString($accountId),
            name: $this->string('name')->toString(),
            type: $this->string('type')->toString(),
            accountNumber: $this->input('account_number') ? $this->string('account_number')->toString() : null,
            providerId: $this->input('provider_id') !== null ? (int) $this->input('provider_id') : null,
            isActive: (bool) $this->input('is_active', true),
        );
    }
}
