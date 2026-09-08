<?php

namespace App\Authentication\Sessions\Infrastructure\Persistence\Doctrine;

use App\Authentication\Sessions\Domain\Session;
use App\Authentication\Sessions\Domain\SessionRepository as SessionRepositoryInterface;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

readonly class SessionRepository extends DoctrineRepository implements SessionRepositoryInterface
{

    public function id(SessionId $id): ?Session
    {
        return $this->repository(Session::class)->find($id);
    }

    public function search(Criteria $criteria): array
    {
        $convertedCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->repository(Session::class)->matching($convertedCriteria)->toArray();
    }

    public function save(Session $session): void
    {
        $this->persist($session);
    }

    public function update(Session $session): void
    {
        $this->persist($session);
    }

    public function delete(Session $session): void
    {
        $this->remove($session);
    }
}
