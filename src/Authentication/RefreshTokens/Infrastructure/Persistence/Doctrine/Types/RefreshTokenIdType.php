<?php

namespace App\Authentication\RefreshTokens\Infrastructure\Persistence\Doctrine\Types;

use App\Authentication\RefreshTokens\Domain\ValueObjects\RefreshTokenId;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\UuidType;

class RefreshTokenIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return RefreshTokenId::class;
    }
}
