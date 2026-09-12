<?php

namespace App\Identity\Clubs\Domain;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Identity\Clubs\Domain\ValueObjects\ClubId;
use App\Shared\Domain\Aggregate\AggregateRoot;

class Club extends AggregateRoot
{
    public function __construct(
        private ClubId $id,
        private ?AccountId $accountId,
    ) {
    }
}
