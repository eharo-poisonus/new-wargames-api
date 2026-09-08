<?php

namespace App\Authentication\RefreshTokens\Domain;

interface RefreshTokenHasher
{
    public function hash(string $token): string;
    public function verify(string $plainToken, string $hashToken): bool;
}
