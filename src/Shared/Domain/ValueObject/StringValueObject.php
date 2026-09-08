<?php

namespace App\Shared\Domain\ValueObject;

use Stringable;

readonly abstract class StringValueObject implements Stringable
{
    final public function __construct(
        protected string $value
    ) {
        $this->validate($value);
    }

    abstract public static function fromString(string $value): self;

    public function value(): string
    {
        return $this->value;
    }

    final public function equals(self $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    abstract protected function validate(string $value): void;
}
