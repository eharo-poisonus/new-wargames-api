<?php

namespace App\Authentication\Accounts\Domain\ValueObjects;

use App\Authentication\Accounts\Domain\Exceptions\InvalidPasswordException;
use Stringable;

readonly class PlainPassword implements Stringable
{
    private const int MIN_LENGTH = 8;

    /** @throws InvalidPasswordException */
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

    public function hash(): string
    {
        return password_hash($this->value, PASSWORD_DEFAULT);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /** @throws InvalidPasswordException */
    private function validate(string $value): void
    {
        if (mb_strlen($value) < self::MIN_LENGTH) {
            throw new InvalidPasswordException();
        }
    }
}
