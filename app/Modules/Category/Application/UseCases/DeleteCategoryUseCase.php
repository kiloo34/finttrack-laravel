<?php

namespace App\Modules\Category\Application\UseCases;

use App\Modules\Category\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

final class DeleteCategoryUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    /**
     * @throws AuthorizationException
     * @throws \DomainException
     */
    public function execute(int $id, int $actingUserId): void
    {
        $category = $this->categoryRepository->findById($id);

        if (! $category) {
            throw new \DomainException('Category not found.');
        }

        if ($category->getUserId() !== $actingUserId) {
            throw new AuthorizationException('You are not authorized to delete this category.');
        }

        $category->ensureCanBeDeleted();

        $this->categoryRepository->delete($id);
    }
}
