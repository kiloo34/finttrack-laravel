<?php

namespace App\Modules\Transfer\Application\DTOs;

use App\Modules\Transfer\Domain\Entities\Transfer;

final class TransferOutput
{
    public function __construct(
        public readonly int $id,
        public readonly int $fromAccountId,
        public readonly int $toAccountId,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $transactionDate,
        public readonly ?string $description,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromDomain(Transfer $transfer): self
    {
        return new self(
            id: $transfer->getId(),
            fromAccountId: $transfer->getFromAccountId(),
            toAccountId: $transfer->getToAccountId(),
            amount: (float) $transfer->getAmount(),
            currency: $transfer->getCurrency(),
            transactionDate: $transfer->getTransactionDate()->format('Y-m-d'),
            description: $transfer->getDescription(),
            createdAt: $transfer->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $transfer->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
