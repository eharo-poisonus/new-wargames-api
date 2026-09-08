<?php

namespace App\Authentication\Sessions\Infrastructure\Persistence\Doctrine\Types;

use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\UuidType;

class SessionIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return SessionId::class;
    }
}
