<?php

namespace App\Authentication\Accounts\Domain\Events;

use App\Shared\Domain\Bus\Event\DomainEvent;

readonly class AccountCreated extends DomainEvent
{
    private string $validationToken;

    public function __construct(
        string $aggregateId,
        string $validationToken,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        $this->validationToken = $validationToken;
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

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
            'validation_token' => $this->validationToken,
            'event_id' => $this->eventId(),
            'occurred_on' => $this->occurredOn()
        ];
    }

    public static function eventName(): string
    {
        return 'account_created';
    }

    public function validationToken(): string
    {
        return $this->validationToken;
    }
}
