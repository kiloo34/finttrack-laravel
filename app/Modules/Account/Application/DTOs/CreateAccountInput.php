<?php

namespace App\Modules\Account\Application\DTOs;

final class CreateAccountInput
{
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
        public readonly string $type,
        public readonly string $currency,
        public readonly ?string $accountNumber,
        public readonly ?int $providerId,
        public readonly string $initialBalance,
        public readonly bool $isActive,
    ) {}
}
