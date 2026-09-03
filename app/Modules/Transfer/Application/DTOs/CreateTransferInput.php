<?php

namespace App\Modules\Transfer\Application\DTOs;

final class CreateTransferInput
{
    public function __construct(
        public readonly int $fromAccountId,
        public readonly int $toAccountId,
        public readonly string $amount,
        public readonly string $currency,
        public readonly \DateTimeImmutable $transactionDate,
        public readonly ?string $description,
    ) {}
}
