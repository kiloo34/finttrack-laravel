<?php

namespace App\Modules\Transaction\Domain\Entities;

use App\Modules\Transaction\Domain\Events\TransactionCreated;
use App\Modules\Transaction\Domain\Events\TransactionDeleted;
use App\Modules\Transaction\Domain\Events\TransactionUpdated;
use App\Modules\Transaction\Domain\ValueObjects\TransactionStatus;
use App\Shared\Domain\Traits\HasDomainEvents;

/**
 * Pure PHP Transaction Domain Entity.
 */
final class Transaction
{
    use HasDomainEvents;

    private function __construct(
        private int $id,
        private int $accountId,
        private ?int $categoryId,
        private string $type,
        private string $amount,
        private string $currency,
        private \DateTimeImmutable $transactionDate,
        private ?string $description,
        private ?string $reference,
        private TransactionStatus $status,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $accountId,
        ?int $categoryId,
        string $type,
        string $amount,
        string $currency,
        \DateTimeImmutable $transactionDate,
        ?string $description = null,
        ?string $reference = null,
        TransactionStatus $status = TransactionStatus::Completed,
    ): self {
        $now = new \DateTimeImmutable;

        return new self(
            id: 0,
            accountId: $accountId,
            categoryId: $categoryId,
            type: $type,
            amount: $amount,
            currency: $currency,
            transactionDate: $transactionDate,
            description: $description,
            reference: $reference,
            status: $status,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function reconstitute(
        int $id,
        int $accountId,
        ?int $categoryId,
        string $type,
        string $amount,
        string $currency,
        \DateTimeImmutable $transactionDate,
        ?string $description,
        ?string $reference,
        TransactionStatus $status,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(...func_get_args());
    }

    public function assignId(int $id): void
    {
        $this->id = $id;
    }

    public function recordCreatedEvent(): void
    {
        $this->recordEvent(new TransactionCreated(
            transactionId: $this->id,
            accountId: $this->accountId,
            amount: (float) $this->amount,
            direction: $this->ledgerDirection(),
            currency: $this->currency,
            status: $this->status->value
        ));
    }

    public function update(
        int $accountId,
        ?int $categoryId,
        string $type,
        string $amount,
        string $currency,
        \DateTimeImmutable $transactionDate,
        ?string $description,
        ?string $reference,
    ): void {
        $this->accountId = $accountId;
        $this->categoryId = $categoryId;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->transactionDate = $transactionDate;
        $this->description = $description;
        $this->reference = $reference;
        $this->updatedAt = new \DateTimeImmutable;

        $this->recordEvent(new TransactionUpdated(
            transactionId: $this->id,
            accountId: $this->accountId,
            amount: (float) $this->amount,
            direction: $this->ledgerDirection(),
            currency: $this->currency,
            status: $this->status->value
        ));
    }

    public function updateStatus(TransactionStatus $status): void
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable;

        $this->recordEvent(new TransactionUpdated(
            transactionId: $this->id,
            accountId: $this->accountId,
            amount: (float) $this->amount,
            direction: $this->ledgerDirection(),
            currency: $this->currency,
            status: $this->status->value
        ));
    }

    public function markAsDeleted(): void
    {
        $this->recordEvent(new TransactionDeleted($this->id, $this->accountId));
    }

    public function ledgerDirection(): string
    {
        if ($this->type === 'income') {
            return 'credit';
        }

        if ($this->type === 'expense') {
            return 'debit';
        }

        if ($this->type === 'adjustment') {
            return (float) $this->amount >= 0 ? 'credit' : 'debit';
        }

        // Default or unknown types
        return 'debit';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getType(): string
    {
        return $this->type;
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getStatus(): TransactionStatus
    {
        return $this->status;
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
