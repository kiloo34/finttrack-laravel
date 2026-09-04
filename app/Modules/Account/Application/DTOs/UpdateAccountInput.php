<?php

namespace App\Modules\Account\Application\DTOs;

use App\Modules\Account\Domain\ValueObjects\AccountId;

final class UpdateAccountInput
{
    public function __construct(
        public readonly AccountId $id,
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $accountNumber,
        public readonly ?int $providerId,
        public readonly bool $isActive,
        public readonly string $syncFrequency,
    ) {}
}
