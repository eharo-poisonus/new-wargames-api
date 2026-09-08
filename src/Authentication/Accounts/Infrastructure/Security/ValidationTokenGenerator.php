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
        return password_hash($token, PASSWORD_DEFAULT);
    }

    public function verify(string $token, string $hash): bool
    {
        return password_verify($token, $hash);
    }
}
