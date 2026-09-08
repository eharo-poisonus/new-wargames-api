<?php

namespace App\Authentication\RefreshTokens\Application\RefreshRefreshToken;

use App\Shared\Domain\Bus\Query\Query;

readonly class RefreshTokenQuery implements Query
{
    public function __construct(
        private string $sessionId,
        private string $refreshToken
    ) {
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function refreshToken(): string
    {
        return $this->refreshToken;
    }
}
