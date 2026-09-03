<?php

namespace App\Modules\Category\Application\DTOs;

use App\Modules\Category\Domain\Entities\Category;

final class CategoryOutput
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $userId,
        public readonly ?int $parentId,
        public readonly string $name,
        public readonly string $type,
        public readonly bool $isSystem,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromDomain(Category $category): self
    {
        return new self(
            id: $category->getId(),
            userId: $category->getUserId(),
            parentId: $category->getParentId(),
            name: $category->getName(),
            type: $category->getType(),
            isSystem: $category->isSystem(),
            createdAt: $category->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $category->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
