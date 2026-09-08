<?php

namespace App\Authentication\RefreshTokens\Domain;

use App\Authentication\RefreshTokens\Domain\ValueObjects\RefreshTokenId;
use App\Shared\Domain\Criteria\Criteria;

interface RefreshTokenRepository
{
    public function id(RefreshTokenId $id): ?RefreshToken;
    public function search(Criteria $criteria): array;
    public function save(RefreshToken $refreshToken): void;
    public function update(RefreshToken $refreshToken): void;
    public function delete(RefreshToken $refreshToken): void;
}
