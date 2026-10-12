<?php

namespace App\Modules\Budget\Application\DTOs;

final class BudgetOutput
{
    public function __construct(
        public readonly int $id,
        public readonly int $categoryId,
        public readonly string $categoryName,
        public readonly float $amount,
        public readonly float $spentAmount,
        public readonly float $remainingAmount,
        public readonly float $percentage,
        public readonly int $month,
        public readonly int $year,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}
}

