<?php

namespace App\Modules\Budget\Application\DTOs;

final class CreateBudgetInput
{
    public function __construct(
        public readonly int $userId,
        public readonly int $categoryId,
        public readonly float $amount,
        public readonly int $month,
        public readonly int $year,
    ) {}
}

