<?php

namespace App\Authentication\RefreshTokens\Application\CreateRefreshToken;

use App\Authentication\RefreshTokens\Domain\RefreshToken;
use App\Authentication\RefreshTokens\Domain\RefreshTokenRepository;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;

final readonly class RefreshTokenCreatorService
{
    public function __construct(
        private RefreshTokenRepository $refreshTokenRepository
    ) {
    }

    public function __invoke(
        SessionId $sessionId,
        string $hashedToken,
        int $ttl
    ): void {
        $refreshToken = RefreshToken::create(
            $sessionId,
            $hashedToken,
            $ttl
        );

        $this->refreshTokenRepository->save($refreshToken);
    }
}
