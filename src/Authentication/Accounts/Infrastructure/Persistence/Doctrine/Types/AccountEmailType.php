<?php

namespace App\Authentication\Accounts\Infrastructure\Persistence\Doctrine\Types;

use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\EmailType;

class AccountEmailType extends EmailType
{
    protected function typeClassName(): string
    {
        return Email::class;
    }
}
