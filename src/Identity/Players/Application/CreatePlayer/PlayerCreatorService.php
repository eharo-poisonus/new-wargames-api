<?php

namespace App\Identity\Players\Application\CreatePlayer;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Identity\Players\Domain\Player;
use App\Identity\Players\Domain\PlayerRepository;
use App\Identity\Players\Domain\ValueObjects\PlayerId;
use App\Identity\Players\Domain\ValueObjects\Username;

final readonly class PlayerCreatorService
{
    public function __construct(
        private PlayerRepository $playerRepository
    ) {
    }

    public function __invoke(PlayerId $playerId, AccountId $accountId, Username $username): void
    {
        $player = Player::create(
            $playerId,
            $accountId,
            $username
        );

        $this->playerRepository->save($player);
    }
}
