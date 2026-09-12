<?php

namespace App\Identity\Players\Domain;

use App\Identity\Players\Domain\ValueObjects\PlayerId;
use App\Shared\Domain\Criteria\Criteria;

interface PlayerRepository
{
    public function id(PlayerId $id): ?Player;
    public function search(Criteria $criteria): array;
    public function searchOne(Criteria $criteria): Player;
    public function save(Player $player): void;
    public function update(Player $player): void;
    public function delete(Player $player): void;
}
