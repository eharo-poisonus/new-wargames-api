<?php

namespace App\Authentication\Accounts\Infrastructure\Persistence\Doctrine\Types;

use App\Authentication\Accounts\Domain\ValueObjects\Username;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\StringValueObjectType;

class UsernameType extends StringValueObjectType
{
    protected function typeClassName(): string
    {
        return Username::class;
    }
}
