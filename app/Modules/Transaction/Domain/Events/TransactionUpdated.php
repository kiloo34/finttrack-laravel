<?php

namespace App\Modules\Transaction\Domain\Events;

use App\Shared\Domain\DomainEventInterface;

final class TransactionUpdated implements DomainEventInterface
{
    private \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly int $transactionId,
        public readonly int $accountId,
        public readonly float $amount,
        public readonly string $direction,
        public readonly string $currency,
        public readonly string $status
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
