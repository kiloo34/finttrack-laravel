<?php

namespace App\Modules\Transfer\Domain\Entities;

/**
 * Pure PHP Transfer Domain Entity.
 */
final class Transfer
{
    private function __construct(
        private readonly int $id,
        private readonly int $fromAccountId,
        private readonly int $toAccountId,
        private readonly string $amount,
        private readonly string $currency,
        private readonly \DateTimeImmutable $transactionDate,
        private readonly ?string $description,
        private readonly \DateTimeImmutable $createdAt,
        private readonly \DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $fromAccountId,
        int $toAccountId,
        string $amount,
        string $currency,
        \DateTimeImmutable $transactionDate,
        ?string $description = null,
    ): self {
        if ($fromAccountId === $toAccountId) {
            throw new \InvalidArgumentException('Cannot transfer to the same account.');
        }

        $now = new \DateTimeImmutable;

        return new self(
            id: 0,
            fromAccountId: $fromAccountId,
            toAccountId: $toAccountId,
            amount: $amount,
            currency: $currency,
            transactionDate: $transactionDate,
            description: $description,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function reconstitute(
        int $id,
        int $fromAccountId,
        int $toAccountId,
        string $amount,
        string $currency,
        \DateTimeImmutable $transactionDate,
        ?string $description,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(...func_get_args());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFromAccountId(): int
    {
        return $this->fromAccountId;
    }

    public function getToAccountId(): int
    {
        return $this->toAccountId;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTransactionDate(): \DateTimeImmutable
    {
        return $this->transactionDate;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
