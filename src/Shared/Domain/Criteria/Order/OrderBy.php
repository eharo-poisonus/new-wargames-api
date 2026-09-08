<?php

namespace App\Shared\Domain\Criteria\Order;

readonly class OrderBy
{
    public function __construct(
        private string $value
    ) {
    }

    final public function value(): string
    {
        return $this->value;
    }
}
