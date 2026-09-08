<?php

namespace App\Shared\Domain\Criteria\FilterValues;

final readonly class NullFilterValue extends FilterValue
{
    public function value(): null
    {
        return null;
    }
}
