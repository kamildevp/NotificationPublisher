<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Event\DomainEvent;

trait RecordsEventsTrait
{
    protected array $events = [];

    public function recordEvent(DomainEvent $event){
        $this->events[] = $event;
    }

    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}