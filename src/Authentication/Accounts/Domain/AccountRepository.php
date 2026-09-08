<?php

namespace App\Authentication\Accounts\Domain;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Shared\Domain\Criteria\Criteria;

interface AccountRepository
{
    public function id(AccountId $id): ?Account;
    public function search(Criteria $criteria): array;
    public function save(Account $account): void;
    public function update(Account $account): void;
    public function delete(Account $account): void;
}
