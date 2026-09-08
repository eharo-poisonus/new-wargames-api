<?php

namespace App\Authentication\Accounts\Domain\Events;

use App\Shared\Domain\Bus\Event\DomainEvent;

readonly class SignedOut extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private string $sessionId,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): DomainEvent {
        return new self($aggregateId, $body['session_id'], $eventId, $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'aggregate_id' => $this->aggregateId(),
            'event_id' => $this->eventId(),
            'occurred_on' => $this->occurredOn(),
            'session_id' => $this->sessionId
        ];
    }

    public static function eventName(): string
    {
        return 'user_signed_out';
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }
}
