<?php

namespace App\Shared\Domain\Criteria\FilterValues;

final readonly class StringFilterValue extends FilterValue
{
    public function __construct(
        private string $value
    ) {
    }

    public function value(): string
    {
        return $this->value;
    }
}
