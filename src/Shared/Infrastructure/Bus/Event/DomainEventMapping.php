<?php

namespace App\Shared\Infrastructure\Bus\Event;

use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\reindex;

final class DomainEventMapping
{
    private array $mapping;

    public function __construct(
        iterable $mapping
    ) {
        $this->mapping = reduce($this->eventsExtractor(), $mapping, []);
    }

    public function for(string $name): string
    {
        if (!isset($this->mapping[$name])) {
            throw new EventNotRegisteredException($name);
        }
        return $this->mapping[$name];
    }

    private function eventsExtractor(): callable
    {
        return fn (array $mapping, DomainEventSubscriber $subscriber): array => array_merge(
            $mapping,
            reindex($this->eventNameExtractor(), $subscriber::subscribedTo())
        );
    }

    private function eventNameExtractor(): callable
    {
        return static fn (string $eventClass): string => $eventClass::eventName();
    }
}
