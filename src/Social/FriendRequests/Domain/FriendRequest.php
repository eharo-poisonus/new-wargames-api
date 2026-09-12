<?php

namespace App\Social\FriendRequests\Domain;

use App\Identity\Players\Domain\ValueObjects\PlayerId;
use App\Social\FriendRequests\Domain\ValueObjects\FriendRequestId;

class FriendRequest
{
    public function __construct(
        private FriendRequestId $id,
        private PlayerId $fromPlayerId,
        private PlayerId $toPlayerId
    ) {
    }
}
