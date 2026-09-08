<?php

namespace App\Shared\Domain\Criteria\Filters;

enum FilterOperator: string
{
    case EQUAL =  '=';
    case NOT_EQUAL =  '!=';
    case GT = '>';
    case GTE = '>=';
    case LT = '<';
    case LTE = '<=';
    case CONTAINS = 'CONTAINS';
    case NOT_CONTAINS = 'NOT_CONTAINS';
    case IN = 'IN';

    public function isContaining(): bool
    {
        return in_array($this->value, [self::CONTAINS->value, self::NOT_CONTAINS->value], true);
    }
}
