<?php

namespace App\Shared\Domain\Traits;

use App\Shared\Domain\DomainEventInterface;

trait HasDomainEvents
{
    /** @var DomainEventInterface[] */
    private array $domainEvents = [];

    protected function recordEvent(DomainEventInterface $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @return DomainEventInterface[]
     */
    public function flushEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
