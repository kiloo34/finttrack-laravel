<?php

namespace App\Modules\Budget\Application\DTOs;

final class UpdateBudgetInput
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly ?int $categoryId = null,
        public readonly ?float $amount = null,
        public readonly ?int $month = null,
        public readonly ?int $year = null,
    ) {}
}

