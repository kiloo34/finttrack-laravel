<?php

namespace App\Modules\Budget\Presentation\Http\Requests;

use App\Modules\Budget\Application\DTOs\CreateBudgetInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBudgetHttpRequest extends FormRequest
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
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($q) {
                    $q->where('user_id', auth()->id())
                        ->orWhere('is_system', true);
                }),
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ];
    }

    public function toDto(): CreateBudgetInput
    {
        /** @var int $userId */
        $userId = (int) auth()->id();

        $month = $this->filled('month')
            ? (int) $this->input('month')
            : (int) now()->format('n');

        $year = $this->filled('year')
            ? (int) $this->input('year')
            : (int) now()->format('Y');

        return new CreateBudgetInput(
            userId: $userId,
            categoryId: (int) $this->input('category_id'),
            amount: (float) $this->input('amount'),
            month: $month,
            year: $year,
        );
    }
}

