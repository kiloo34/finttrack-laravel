<?php

namespace App\Modules\Transaction\Application\DTOs;

final class CreateTransactionInput
{
    public function __construct(
        public readonly int $accountId,
        public readonly ?int $categoryId,
        public readonly string $type,
        public readonly string $amount,
        public readonly string $currency,
        public readonly \DateTimeImmutable $transactionDate,
        public readonly ?string $description,
        public readonly ?string $reference,
        public readonly string $status,
    ) {}
}
