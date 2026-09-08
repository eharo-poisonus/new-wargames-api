<?php

namespace App\Shared\Domain\Criteria\Order;

enum OrderType: string
{
    case ASC  = 'ASC';
    case DESC = 'DESC';
    case NONE = 'none';

    public function isNone(): bool
    {
        return $this->value === OrderType::NONE->value;
    }
}
