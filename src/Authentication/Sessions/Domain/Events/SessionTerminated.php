<?php

namespace App\Authentication\Sessions\Domain\Events;

use App\Shared\Domain\Bus\Event\DomainEvent;

readonly class SessionTerminated extends DomainEvent
{
    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): DomainEvent {
        return new self($aggregateId, $eventId, $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'aggregate_id' => $this->aggregateId(),
            'event_id' => $this->eventId(),
            'occurred_on' => $this->occurredOn()
        ];
    }

    public static function eventName(): string
    {
        return 'session_terminated';
    }
}
