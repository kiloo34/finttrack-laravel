<?php

namespace App\Shared\Domain;

interface DomainEventInterface
{
    public function occurredAt(): \DateTimeImmutable;
}
