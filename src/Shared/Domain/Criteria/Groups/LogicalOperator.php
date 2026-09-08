<?php

namespace App\Shared\Domain\Criteria\Groups;

enum LogicalOperator: string
{
    case AND =  'AND';
    case OR =  'OR';
}
