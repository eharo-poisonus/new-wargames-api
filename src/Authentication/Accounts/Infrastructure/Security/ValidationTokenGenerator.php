<?php

namespace App\Authentication\Accounts\Infrastructure\Security;

use App\Authentication\Accounts\Domain\ValidationTokenGenerator as ValidationTokenGeneratorInterface;

class ValidationTokenGenerator implements ValidationTokenGeneratorInterface
{
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    public function verify(string $token, string $hash): bool
    {
        $hashedInput = hash('sha256', $token);

        return hash_equals($hash, $hashedInput);
    }
}
