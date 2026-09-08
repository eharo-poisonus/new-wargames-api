<?php

namespace App\Authentication\RefreshTokens\Infrastructure\Persistence\Doctrine;

use App\Authentication\RefreshTokens\Domain\RefreshToken;
use App\Authentication\RefreshTokens\Domain\RefreshTokenRepository as RefreshTokenRepositoryInterface;
use App\Authentication\RefreshTokens\Domain\ValueObjects\RefreshTokenId;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

readonly class RefreshTokenRepository extends DoctrineRepository implements RefreshTokenRepositoryInterface
{

    public function id(RefreshTokenId $id): ?RefreshToken
    {
        return $this->repository(RefreshToken::class)->find($id);
    }

    public function search(Criteria $criteria): array
    {
        $convertedCriteria = DoctrineCriteriaConverter::convert($criteria);
        return $this->repository(RefreshToken::class)->matching($convertedCriteria)->toArray();
    }

    public function save(RefreshToken $refreshToken): void
    {
        $this->persist($refreshToken);
    }

    public function update(RefreshToken $refreshToken): void
    {
        $this->persist($refreshToken);
    }

    public function delete(RefreshToken $refreshToken): void
    {
        $this->persist($refreshToken);
    }
}
