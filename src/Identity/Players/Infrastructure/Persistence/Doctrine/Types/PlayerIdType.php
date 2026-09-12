<?php

namespace App\Identity\Players\Infrastructure\Persistence\Doctrine\Types;

use App\Identity\Players\Domain\ValueObjects\PlayerId;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\UuidType;

class PlayerIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return PlayerId::class;
    }
}
