<?php

namespace App\Modules\Budget\Presentation\Http\Requests;

use App\Modules\Budget\Application\DTOs\UpdateBudgetInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBudgetHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($q) {
                    $q->where('user_id', auth()->id())
                        ->orWhere('is_system', true);
                }),
            ],
            'amount' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'month' => ['sometimes', 'required', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'required', 'integer', 'min:2000', 'max:2100'],
        ];
    }

    public function toDto(int $id): UpdateBudgetInput
    {
        /** @var int $userId */
        $userId = (int) auth()->id();

        return new UpdateBudgetInput(
            id: $id,
            userId: $userId,
            categoryId: $this->has('category_id') ? (int) $this->input('category_id') : null,
            amount: $this->has('amount') ? (float) $this->input('amount') : null,
            month: $this->has('month') ? (int) $this->input('month') : null,
            year: $this->has('year') ? (int) $this->input('year') : null,
        );
    }
}

