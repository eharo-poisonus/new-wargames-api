<?php

namespace App\Authentication\Sessions\Domain;

use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Criteria\Criteria;

interface SessionRepository
{
    public function id(SessionId $id): ?Session;
    public function search(Criteria $criteria): array;
    public function save(Session $session): void;
    public function update(Session $session): void;
    public function delete(Session $session): void;
}
