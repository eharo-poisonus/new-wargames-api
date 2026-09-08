<?php

namespace App\Authentication\RefreshTokens\Application\RefreshRefreshToken;

use App\Shared\Domain\Bus\Query\Response;

readonly class RefreshTokenResponse implements Response
{
    public function __construct(
        private string $accessToken,
        private string $refreshToken,
        private string $sessionId
    ) {
    }

    public function refreshToken(): string
    {
        return $this->refreshToken;
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function jsonSerialize(): array
    {
        return [
            'access_token' => $this->accessToken
        ];
    }
}
