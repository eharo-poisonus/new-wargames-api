<?php

namespace App\Shared\Domain\Criteria\FilterValues;

use InvalidArgumentException;

abstract readonly class FilterValue
{
    public static function fromValue(mixed $value): static
    {
        return match (gettype($value)) {
            'string' => new StringFilterValue($value),
            'integer' => new IntegerFilterValue($value),
            'double' => new FloatFilterValue($value),
            'boolean' => new BooleanFilterValue($value),
            'NULL' => new NullFilterValue(),
            'array' => new ArrayFilterValue($value),
            'object' => new ObjectFilterValue($value),
            default => throw new InvalidArgumentException('Invalid filter value type')
        };
    }

    abstract public function value(): mixed;
}
