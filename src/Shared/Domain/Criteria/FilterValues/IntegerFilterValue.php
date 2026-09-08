<?php

namespace App\Shared\Domain\Criteria\FilterValues;

final readonly class IntegerFilterValue extends FilterValue
{
    public function __construct(
        private int $value
    ) {
    }

    public function value(): int
    {
        return $this->value;
    }
}
