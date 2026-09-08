<?php

namespace App\Shared\Domain\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Stringable;

abstract readonly class Uuid implements Stringable
{
    final public function __construct(
        protected string $value
    ) {
        $this->ensureIsValidUuid($value);
    }

    abstract public static function fromString(string $value): self;

    public static function random(): static
    {
        return new static(RamseyUuid::Uuid4()->toString());
    }

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

    private function ensureIsValidUuid(string $id): void
    {
        if (!RamseyUuid::isValid($id)) {
            throw new InvalidArgumentException(
                sprintf('Invalid uuid: <%s>.', $id)
            );
        }
    }
}
