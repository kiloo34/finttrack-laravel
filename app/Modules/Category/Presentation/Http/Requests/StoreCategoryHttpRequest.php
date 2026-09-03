<?php

namespace App\Modules\Category\Presentation\Http\Requests;

use App\Modules\Category\Application\DTOs\CreateCategoryInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryHttpRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(fn ($q) => $q->where('user_id', auth()->id())),
            ],
            'type' => ['required', 'string', Rule::exists('category_types', 'value')],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
        ];
    }

    public function toDto(): CreateCategoryInput
    {
        /** @var int<1, max> $userId */
        $userId = auth()->id();

        return new CreateCategoryInput(
            userId: $userId,
            name: $this->string('name')->toString(),
            type: $this->string('type')->toString(),
            parentId: $this->input('parent_id') !== null ? (int) $this->input('parent_id') : null,
        );
    }
}
