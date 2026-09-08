<?php

namespace App\Authentication\Accounts\Domain;

interface ValidationTokenGenerator
{
    public function generate(): string;
    public function hash(string $token): string;
    public function verify(string $token, string $hash): bool;
}
