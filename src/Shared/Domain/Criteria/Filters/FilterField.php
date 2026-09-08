<?php

namespace App\Shared\Domain\Criteria\Filters;

readonly class FilterField
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
