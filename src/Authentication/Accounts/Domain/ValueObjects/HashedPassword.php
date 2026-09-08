<?php

namespace App\Authentication\Accounts\Domain\ValueObjects;

readonly class HashedPassword
{
    public function __construct(
        private string $value
    ) {
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function verify(PlainPassword $plainPassword): bool
    {
        return password_verify($plainPassword, $this->value);
    }
}
