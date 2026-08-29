<?php

namespace App\Modules\Category\Infrastructure\Persistence\Repositories;

use App\Modules\Category\Domain\Entities\Category;
use App\Modules\Category\Domain\Repositories\CategoryRepositoryInterface;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Mappers\CategoryMapper;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function save(Category $category): Category
    {
        if ($category->getId() === 0) {
            // New entity — create
            $model = CategoryModel::create(CategoryMapper::toPersistence($category));
        } else {
            $model = CategoryModel::query()->findOrFail($category->getId());
            $model->update(CategoryMapper::toPersistence($category));
        }

        return CategoryMapper::toDomain($model);
    }

    public function findById(int $id): ?Category
    {
        $model = CategoryModel::query()->find($id);

        return $model ? CategoryMapper::toDomain($model) : null;
    }

    /** @return Category[] */
    public function findAvailableForUser(int $userId): array
    {
        return CategoryModel::query()
            ->availableFor($userId)
            ->get()
            ->map(fn (CategoryModel $model) => CategoryMapper::toDomain($model))
            ->all();
    }

    public function delete(int $id): void
    {
        CategoryModel::query()->where('id', $id)->delete();
    }
}
