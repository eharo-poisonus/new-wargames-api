<?php

namespace App\Authentication\Sessions\Domain\Events;

use App\Shared\Domain\Bus\Event\DomainEvent;

readonly class SessionCreated extends DomainEvent
{
    private string $refreshTokenHash;

    public function __construct(
        string $aggregateId,
        string $hashedToken,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        $this->refreshTokenHash = $hashedToken;
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): DomainEvent {
        return new self($aggregateId, '', $eventId, $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'aggregate_id' => $this->aggregateId(),
            'hashed_token' => $this->refreshTokenHash,
            'event_id' => $this->eventId(),
            'occurred_on' => $this->occurredOn()
        ];
    }

    public static function eventName(): string
    {
        return 'session_created';
    }

    public function refreshTokenHash(): string
    {
        return $this->refreshTokenHash;
    }
}
