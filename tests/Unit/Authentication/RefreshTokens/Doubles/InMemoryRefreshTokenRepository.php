<?php

namespace App\Tests\Unit\Authentication\RefreshTokens\Doubles;

use App\Authentication\RefreshTokens\Domain\RefreshToken;
use App\Authentication\RefreshTokens\Domain\RefreshTokenRepository;
use App\Authentication\RefreshTokens\Domain\ValueObjects\RefreshTokenId;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filters\Filter;
use App\Shared\Domain\ValueObject\Uuid;
use InvalidArgumentException;

final class InMemoryRefreshTokenRepository implements RefreshTokenRepository
{
    private array $refreshTokens = [];

    public function id(RefreshTokenId $id): ?RefreshToken
    {
        return $this->refreshTokens[$id->value()] ?? null;
    }

    public function search(Criteria $criteria): array
    {
        return array_values(
            array_filter(
                $this->refreshTokens,
                fn (RefreshToken $refreshToken) => $this->matches($refreshToken, $criteria)
            )
        );
    }

    public function save(RefreshToken $refreshToken): void
    {
        $this->refreshTokens[$refreshToken->id()->value()] = $refreshToken;
    }

    public function update(RefreshToken $refreshToken): void
    {
        $this->save($refreshToken);
    }

    public function delete(RefreshToken $refreshToken): void
    {
        unset($this->refreshTokens[$refreshToken->id()->value()]);
    }

    public function all(): array
    {
        return array_values($this->refreshTokens);
    }

    private function matches(RefreshToken $refreshToken, Criteria $criteria): bool
    {
        foreach ($criteria->filtersGroups() as $filtersGroup) {
            foreach ($filtersGroup->filters() as $filter) {
                if (!$this->matchesFilter($refreshToken, $filter)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function matchesFilter(RefreshToken $refreshToken, Filter $filter): bool
    {
        $actual = match ($filter->field()->value()) {
            'hashedToken' => $refreshToken->hashedToken(),
            'sessionId' => $refreshToken->sessionId(),
            'revokedAt' => $refreshToken->revokedAt(),
            default => throw new InvalidArgumentException(
                sprintf('Unsupported filter field: %s', $filter->field()->value())
            )
        };

        $expected = $filter->value()->value();

        if ($actual instanceof Uuid && $expected instanceof Uuid) {
            return $actual->equals($expected);
        }

        return $actual === $expected;
    }
}
