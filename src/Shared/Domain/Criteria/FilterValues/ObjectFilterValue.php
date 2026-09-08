<?php

namespace App\Shared\Domain\Criteria\FilterValues;

final readonly class ObjectFilterValue extends FilterValue
{
    public function __construct(
        private object $value
    ) {
    }

    public function value(): object
    {
        return $this->value;
    }
}
