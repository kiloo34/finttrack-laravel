<?php

namespace App\Modules\Category\Application\DTOs;

final class CreateCategoryInput
{
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
        public readonly string $type,
        public readonly ?int $parentId = null,
    ) {}
}
