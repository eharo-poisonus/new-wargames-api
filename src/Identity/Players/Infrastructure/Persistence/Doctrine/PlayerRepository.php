<?php

namespace App\Identity\Players\Infrastructure\Persistence\Doctrine;

use App\Identity\Players\Domain\Player;
use App\Identity\Players\Domain\PlayerRepository as PlayerRepositoryInterface;
use App\Identity\Players\Domain\ValueObjects\PlayerId;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

readonly class PlayerRepository extends DoctrineRepository implements PlayerRepositoryInterface
{
    public function id(PlayerId $id): ?Player
    {
        return $this->repository(Player::class)->find($id);
    }

    public function search(Criteria $criteria): array
    {
        $convertedCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->repository(Player::class)->matching($convertedCriteria)->toArray();
    }

    public function searchOne(Criteria $criteria): Player
    {
        $convertedCriteria = DoctrineCriteriaConverter::convert($criteria);
        $players = $this->repository(Player::class)->matching($convertedCriteria)->toArray();

        if (count($players) !== 1) {
            throw new \RuntimeException('Player not found');
        }

        return array_shift($players);
    }

    public function save(Player $player): void
    {
        $this->persist($player);
    }

    public function update(Player $player): void
    {
        $this->persist($player);
    }

    public function delete(Player $player): void
    {
        $this->remove($player);
    }
}
