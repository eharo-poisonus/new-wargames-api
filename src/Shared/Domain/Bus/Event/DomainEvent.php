<?php

namespace App\Shared\Domain\Bus\Event;

use App\Shared\Domain\Utils;
use App\Shared\Domain\ValueObject\SimpleUuid;
use DateTimeImmutable;

abstract readonly class DomainEvent
{
    private string $eventId;
    private string $occurredOn;

    public function __construct(
        private string $aggregateId,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        $this->eventId = $eventId ?: SimpleUuid::random()->value();
        $this->occurredOn = $occurredOn ?: Utils::dateToString(new DateTimeImmutable());
    }

    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self;

    abstract public function toPrimitives(): array;

    abstract public static function eventName(): string;

    final public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    final public function eventId(): string
    {
        return $this->eventId;
    }

    final public function occurredOn(): string
    {
        return $this->occurredOn;
    }
}
