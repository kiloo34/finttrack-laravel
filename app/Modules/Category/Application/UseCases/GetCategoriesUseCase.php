<?php

namespace App\Modules\Category\Application\UseCases;

use App\Modules\Category\Application\DTOs\CategoryOutput;
use App\Modules\Category\Domain\Repositories\CategoryRepositoryInterface;

final class GetCategoriesUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    /** @return CategoryOutput[] */
    public function execute(int $userId): array
    {
        $categories = $this->categoryRepository->findAvailableForUser($userId);

        return array_map(
            fn ($category) => CategoryOutput::fromDomain($category),
            $categories,
        );
    }
}
