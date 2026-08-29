<?php

namespace App\Modules\Category\Application\DTOs;

final class UpdateCategoryInput
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $type,
        public readonly ?int $parentId = null,
    ) {}
}
