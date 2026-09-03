<?php

namespace App\Modules\Category\Presentation\Http\Requests;

use App\Modules\Category\Application\DTOs\UpdateCategoryInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryHttpRequest extends FormRequest
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
                Rule::unique('categories')
                    ->where(fn ($q) => $q->where('user_id', auth()->id()))
                    ->ignore($this->route('category')),
            ],
            'type' => ['required', 'string', Rule::exists('category_types', 'value')],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
        ];
    }

    public function toDto(int $id): UpdateCategoryInput
    {
        return new UpdateCategoryInput(
            id: $id,
            name: $this->string('name')->toString(),
            type: $this->string('type')->toString(),
            parentId: $this->input('parent_id') !== null ? (int) $this->input('parent_id') : null,
        );
    }
}
