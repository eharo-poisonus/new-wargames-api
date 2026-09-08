<?php

namespace App\Shared\Infrastructure\Bus\Event;

use RuntimeException;

final class EventNotRegisteredException extends RuntimeException
{
    public function __construct(string $eventName)
    {
        parent::__construct("The domain event class for <$eventName> does not exist or have no subscribers.");
    }
}
