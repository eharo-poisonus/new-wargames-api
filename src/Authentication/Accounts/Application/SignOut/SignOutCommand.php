<?php

namespace App\Authentication\Accounts\Application\SignOut;

use App\Shared\Domain\Bus\Command\Command;

readonly class SignOutCommand implements Command
{
    public function __construct(
        private string $sessionId,
        private string $refreshTokenId,
        private string $accessToken
    ) {
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function refreshTokenId(): string
    {
        return $this->refreshTokenId;
    }

    public function accessToken(): string
    {
        return $this->accessToken;
    }
}
