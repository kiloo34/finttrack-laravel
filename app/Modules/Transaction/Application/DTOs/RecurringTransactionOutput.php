<?php

namespace App\Modules\Transaction\Application\DTOs;

use App\Modules\Transaction\Domain\Entities\RecurringTransaction;

final readonly class RecurringTransactionOutput
{
    public function __construct(
        public int $id,
        public int $userId,
        public int $accountId,
        public ?int $destinationAccountId,
        public ?int $categoryId,
        public string $type,
        public string $amount,
        public string $currency,
        public ?string $description,
        public string $frequency,
        public string $executionType,
        public string $nextRunDate,
        public bool $isActive,
    ) {}

    public static function fromDomain(RecurringTransaction $transaction): self
    {
        return new self(
            id: $transaction->getId(),
            userId: $transaction->getUserId(),
            accountId: $transaction->getAccountId(),
            destinationAccountId: $transaction->getDestinationAccountId(),
            categoryId: $transaction->getCategoryId(),
            type: $transaction->getType(),
            amount: $transaction->getAmount(),
            currency: $transaction->getCurrency(),
            description: $transaction->getDescription(),
            frequency: $transaction->getFrequency()->value,
            executionType: $transaction->getExecutionType()->value,
            nextRunDate: $transaction->getNextRunDate()->format('Y-m-d'),
            isActive: $transaction->isActive()
        );
    }
}
