<?php

namespace App\Authentication\Accounts\Domain\ValueObjects;

use App\Shared\Domain\ValueObject\SimpleEmail;

readonly class Email extends SimpleEmail
{
    public static function fromString(string $value): Email
    {
        return new self($value);
    }
}
