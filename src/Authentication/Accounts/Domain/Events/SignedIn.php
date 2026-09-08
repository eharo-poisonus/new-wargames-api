<?php

namespace App\Authentication\Accounts\Domain\Events;

use App\Shared\Domain\Bus\Event\DomainEvent;

readonly class SignedIn extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private string $sessionId,
        private string $device,
        private string $ipAddress,
        private string $hashedRefreshToken,
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
        return new self($aggregateId,'', '', '', '', $eventId, $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'aggregate_id' => $this->aggregateId(),
            'event_id' => $this->eventId(),
            'occurred_on' => $this->occurredOn(),
            'session_id' => $this->sessionId,
            'device' => $this->device,
            'ip_address' => $this->ipAddress,
            'hashed_refresh_token' => $this->hashedRefreshToken
        ];
    }

    public static function eventName(): string
    {
        return 'user_signed_in';
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function device(): string
    {
        return $this->device;
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }

    public function hashedRefreshToken(): string
    {
        return $this->hashedRefreshToken;
    }
}
