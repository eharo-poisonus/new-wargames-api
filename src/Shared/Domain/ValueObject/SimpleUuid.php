<?php

namespace App\Shared\Domain\ValueObject;

readonly class SimpleUuid extends Uuid
{
    public static function fromString(string $value): static
    {
        return new static($value);
    }
}
