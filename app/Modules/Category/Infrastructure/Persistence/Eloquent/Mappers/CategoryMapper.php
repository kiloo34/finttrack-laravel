<?php

namespace App\Modules\Category\Infrastructure\Persistence\Eloquent\Mappers;

use App\Modules\Category\Domain\Entities\Category;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;

final class CategoryMapper
{
    public static function toDomain(CategoryModel $model): Category
    {
        return Category::reconstitute(
            id: (int) $model->id,
            userId: $model->user_id ? (int) $model->user_id : null,
            parentId: $model->parent_id ? (int) $model->parent_id : null,
            name: (string) $model->name,
            type: $model->type,
            isSystem: (bool) $model->is_system,
            createdAt: new \DateTimeImmutable($model->created_at?->toDateTimeString() ?? 'now'),
            updatedAt: new \DateTimeImmutable($model->updated_at?->toDateTimeString() ?? 'now'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPersistence(Category $category): array
    {
        return [
            'user_id' => $category->getUserId(),
            'parent_id' => $category->getParentId(),
            'name' => $category->getName(),
            'type' => $category->getType(),
            'is_system' => $category->isSystem(),
        ];
    }
}
