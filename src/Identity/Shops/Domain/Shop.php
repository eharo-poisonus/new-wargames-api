<?php

namespace App\Identity\Shops\Domain;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Identity\Shops\Domain\ValueObjects\ShopId;
use App\Shared\Domain\Aggregate\AggregateRoot;

class Shop extends AggregateRoot
{
    public function __construct(
        private ShopId $id,
        private ?AccountId $accountId,
    ) {
    }
}
