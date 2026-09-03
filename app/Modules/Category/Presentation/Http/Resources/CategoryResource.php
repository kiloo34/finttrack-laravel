<?php

namespace App\Modules\Category\Presentation\Http\Resources;

use App\Modules\Category\Application\DTOs\CategoryOutput;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CategoryOutput */
class CategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CategoryOutput $output */
        $output = $this->resource;

        return [
            'id' => $output->id,
            'name' => $output->name,
            'type' => $output->type,
            'parent_id' => $output->parentId,
            'is_system' => $output->isSystem,
            'created_at' => $output->createdAt,
            'updated_at' => $output->updatedAt,
        ];
    }
}
