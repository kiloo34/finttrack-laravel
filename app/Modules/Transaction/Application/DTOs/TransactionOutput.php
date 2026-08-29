<?php

namespace App\Modules\Transaction\Application\DTOs;

use App\Modules\Transaction\Domain\Entities\Transaction;

final class TransactionOutput
{
    public function __construct(
        public readonly int $id,
        public readonly int $accountId,
        public readonly ?int $categoryId,
        public readonly string $type,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $transactionDate,
        public readonly ?string $description,
        public readonly ?string $reference,
        public readonly string $status,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromDomain(Transaction $transaction): self
    {
        return new self(
            id: $transaction->getId(),
            accountId: $transaction->getAccountId(),
            categoryId: $transaction->getCategoryId(),
            type: $transaction->getType(),
            amount: (float) $transaction->getAmount(),
            currency: $transaction->getCurrency(),
            transactionDate: $transaction->getTransactionDate()->format('Y-m-d'),
            description: $transaction->getDescription(),
            reference: $transaction->getReference(),
            status: $transaction->getStatus()->value,
            createdAt: $transaction->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $transaction->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
