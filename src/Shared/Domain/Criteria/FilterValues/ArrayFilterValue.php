<?php

namespace App\Shared\Domain\Criteria\FilterValues;

final readonly class ArrayFilterValue extends FilterValue
{
    public function __construct(
        private array $value
    ) {
    }

    public function value(): array
    {
        return $this->value;
    }
}
