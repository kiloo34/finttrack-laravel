<?php

namespace App\Modules\Category\Application\UseCases;

use App\Modules\Category\Application\DTOs\CategoryOutput;
use App\Modules\Category\Application\DTOs\UpdateCategoryInput;
use App\Modules\Category\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

final class UpdateCategoryUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    /**
     * @throws AuthorizationException
     * @throws \DomainException
     */
    public function execute(UpdateCategoryInput $input, int $actingUserId): CategoryOutput
    {
        $category = $this->categoryRepository->findById($input->id);

        if (! $category) {
            throw new \DomainException('Category not found.');
        }

        if ($category->getUserId() !== $actingUserId) {
            throw new AuthorizationException('You are not authorized to update this category.');
        }

        $category->update(name: $input->name, type: $input->type, parentId: $input->parentId);

        $saved = $this->categoryRepository->save($category);

        return CategoryOutput::fromDomain($saved);
    }
}
