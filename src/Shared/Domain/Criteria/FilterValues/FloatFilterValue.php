<?php

namespace App\Shared\Domain\Criteria\FilterValues;

final readonly class FloatFilterValue extends FilterValue
{
    public function __construct(
        private float $value
    ) {
    }

    public function value(): float
    {
        return $this->value;
    }
}
