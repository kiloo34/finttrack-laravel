<?php

namespace App\Modules\Account\Application\DTOs;

use App\Modules\Account\Domain\Entities\Account;

final class AccountOutput
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly string $name,
        public readonly string $type,
        public readonly string $currency,
        public readonly ?string $accountNumber,
        public readonly ?int $providerId,
        public readonly ?string $providerName,
        public readonly float $initialBalance,
        public readonly bool $isActive,
        public readonly string $syncFrequency,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromDomain(Account $account): self
    {
        return new self(
            id: (int) $account->getId()->value(),
            userId: $account->getUserId(),
            name: $account->getName(),
            type: $account->getType(),
            currency: $account->getCurrency(),
            accountNumber: $account->getAccountNumber(),
            providerId: $account->getProviderId(),
            providerName: $account->getProviderName(),
            initialBalance: (float) $account->getInitialBalance(),
            isActive: $account->isActive(),
            syncFrequency: $account->getSyncFrequency(),
            createdAt: $account->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $account->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
