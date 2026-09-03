<?php

namespace App\Modules\Category\Domain\Repositories;

use App\Modules\Category\Domain\Entities\Category;

interface CategoryRepositoryInterface
{
    public function save(Category $category): Category;

    public function findById(int $id): ?Category;

    /** @return Category[] */
    public function findAvailableForUser(int $userId): array;

    public function delete(int $id): void;
}
