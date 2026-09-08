<?php

namespace App\Shared\Domain\Criteria\FilterValues;

final readonly class BooleanFilterValue extends FilterValue
{
    public function __construct(
        private bool $value
    ) {
    }

    public function value(): bool
    {
        return $this->value;
    }
}
