<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidEmailException;

readonly class SimpleEmail extends StringValueObject
{
    public static function fromString(string $value): StringValueObject
    {
        return new static($value);
    }

    /** @throws InvalidEmailException */
    protected function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }
    }
}
