<?php

namespace App\Authentication\RefreshTokens\Infrastructure\Tokens;

use App\Authentication\RefreshTokens\Domain\RefreshTokenGenerator as RefreshTokenGeneratorInterface;

class RefreshTokenGenerator implements RefreshTokenGeneratorInterface
{
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}
