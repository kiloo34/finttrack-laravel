<?php

namespace App\Modules\Account\Domain\Entities;

use App\Modules\Account\Domain\ValueObjects\AccountId;

/**
 * Pure PHP Domain Entity — no Eloquent, no framework dependencies.
 */
final class Account
{
    private function __construct(
        private readonly AccountId $id,
        private readonly int $userId,
        private string $name,
        private string $type,
        private string $currency,
        private ?string $accountNumber,
        private ?int $providerId,
        private ?string $providerName,
        private string $initialBalance,
        private bool $isActive,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $userId,
        string $name,
        string $type,
        string $currency = 'IDR',
        ?string $accountNumber = null,
        ?int $providerId = null,
        ?string $providerName = null,
        string $initialBalance = '0.00',
        bool $isActive = true,
    ): self {
        $now = new \DateTimeImmutable;

        return new self(
            id: AccountId::generate(),
            userId: $userId,
            name: $name,
            type: $type,
            currency: $currency,
            accountNumber: $accountNumber,
            providerId: $providerId,
            providerName: $providerName,
            initialBalance: $initialBalance,
            isActive: $isActive,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * Reconstitute from persistence (hydration).
     */
    public static function reconstitute(
        AccountId $id,
        int $userId,
        string $name,
        string $type,
        string $currency,
        ?string $accountNumber,
        ?int $providerId,
        ?string $providerName,
        string $initialBalance,
        bool $isActive,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            userId: $userId,
            name: $name,
            type: $type,
            currency: $currency,
            accountNumber: $accountNumber,
            providerId: $providerId,
            providerName: $providerName,
            initialBalance: $initialBalance,
            isActive: $isActive,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function update(string $name, string $type, ?string $accountNumber, ?int $providerId, ?string $providerName, bool $isActive): void
    {
        $this->name = $name;
        $this->type = $type;
        $this->accountNumber = $accountNumber;
        $this->providerId = $providerId;
        $this->providerName = $providerName;
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTimeImmutable;
    }

    // Getters
    public function getId(): AccountId
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function getProviderId(): ?int
    {
        return $this->providerId;
    }

    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    public function getInitialBalance(): string
    {
        return $this->initialBalance;
    }

    public function isActive(): bool
    {
        return $this->isActive;
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
