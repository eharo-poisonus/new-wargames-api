<?php

namespace App\Identity\Players\Infrastructure\Persistence\Doctrine\Types;

use App\Identity\Players\Domain\ValueObjects\Username;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\StringValueObjectType;

class UsernameType extends StringValueObjectType
{
    protected function typeClassName(): string
    {
        return Username::class;
    }
}
