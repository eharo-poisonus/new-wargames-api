<?php

namespace App\Authentication\Accounts\Infrastructure\Persistence\Doctrine;

use App\Authentication\Accounts\Domain\Account;
use App\Authentication\Accounts\Domain\AccountRepository as AccountRepositoryInterface;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

readonly class AccountRepository extends DoctrineRepository implements AccountRepositoryInterface
{
    public function id(AccountId $id): ?Account
    {
        return $this->repository(Account::class)->find($id);
    }

    public function search(Criteria $criteria): array
    {
        $convertedCriteria = DoctrineCriteriaConverter::convert($criteria);

        return $this->repository(Account::class)->matching($convertedCriteria)->toArray();
    }

    public function save(Account $account): void
    {
        $this->persist($account);
    }

    public function update(Account $account): void
    {
        $this->persist($account);
    }

    public function delete(Account $account): void
    {
        $this->remove($account);
    }
}
