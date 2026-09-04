<?php

namespace App\Modules\Transaction\Application\DTOs;

final class TransactionSummaryOutput
{
    public function __construct(
        public readonly string $income,
        public readonly string $expense,
    ) {}
}
