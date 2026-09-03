<?php

namespace App\Modules\Transaction\Domain\Events;

use App\Shared\Domain\DomainEventInterface;

final class TransactionDeleted implements DomainEventInterface
{
    private \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly int $transactionId
    ) {
        $this->occurredAt = new \DateTimeImmutable;
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
