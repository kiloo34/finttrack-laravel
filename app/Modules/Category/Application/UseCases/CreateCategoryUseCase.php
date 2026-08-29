<?php

namespace App\Modules\Category\Application\UseCases;

use App\Modules\Category\Application\DTOs\CategoryOutput;
use App\Modules\Category\Application\DTOs\CreateCategoryInput;
use App\Modules\Category\Domain\Entities\Category;
use App\Modules\Category\Domain\Repositories\CategoryRepositoryInterface;

final class CreateCategoryUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function execute(CreateCategoryInput $input): CategoryOutput
    {
        $category = Category::create(
            userId: $input->userId,
            name: $input->name,
            type: $input->type,
            parentId: $input->parentId,
        );

        $saved = $this->categoryRepository->save($category);

        return CategoryOutput::fromDomain($saved);
    }
}
