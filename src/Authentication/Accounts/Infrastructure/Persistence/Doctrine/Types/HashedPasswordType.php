<?php

namespace App\Authentication\Accounts\Infrastructure\Persistence\Doctrine\Types;

use App\Authentication\Accounts\Domain\ValueObjects\HashedPassword;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\StringValueObjectType;

class HashedPasswordType extends StringValueObjectType
{
    protected function typeClassName(): string
    {
        return HashedPassword::class;
    }
}
