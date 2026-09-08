<?php

namespace App\Authentication\Accounts\Domain;

interface AccessTokenUtils
{
    public function generate(AccessTokenClaims $claims): string;
    public function verify(string $token): bool;
    public function retrieveClaims(string $token): AccessTokenClaims;
}
