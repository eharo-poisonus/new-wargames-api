<?php

namespace App\Authentication\Accounts\Domain\ValueObjects;

use App\Authentication\Accounts\Domain\Exceptions\InvalidUsernameException;
use Stringable;

readonly class Username implements Stringable
{
    private const int MIN_LENGTH = 4;

    /** @throws InvalidUsernameException */
    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /** @throws InvalidUsernameException */
    private function validate(string $value): void
    {
        if (mb_strlen($value) < self::MIN_LENGTH) {
            throw new InvalidUsernameException();
        }
    }
}
