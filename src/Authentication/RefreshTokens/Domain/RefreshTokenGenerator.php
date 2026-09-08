<?php

namespace App\Authentication\RefreshTokens\Domain;

interface RefreshTokenGenerator
{
    public function generate(): string;
}
