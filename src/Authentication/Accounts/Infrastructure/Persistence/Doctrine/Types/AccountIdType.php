<?php

namespace App\Authentication\Accounts\Infrastructure\Persistence\Doctrine\Types;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\UuidType;

class AccountIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return AccountId::class;
    }
}
