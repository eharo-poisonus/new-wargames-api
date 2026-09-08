<?php

namespace App\Authentication\RefreshTokens\Infrastructure\Tokens;

use App\Authentication\RefreshTokens\Domain\RefreshTokenHasher;

class Sha256TokenHasher implements RefreshTokenHasher
{
    public function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    public function verify(string $plainToken, string $hashToken): bool
    {
        $hashedInput = hash('sha256', $plainToken);

        return hash_equals($hashToken, $hashedInput);
    }
}
