<?php

namespace App\Authentication\RefreshTokens\Domain\ValueObjects;

final readonly class PlainRefreshToken
{
    private const int BYTES_LENGTH = 64;

    private function __construct(
        private string $value,
        private string $hash
    ) {
    }

    public static function generate(): self
    {
        $value = bin2hex(random_bytes(self::BYTES_LENGTH));

        return new self($value, self::hashOf($value));
    }

    private static function hashOf(string $plainValue): string
    {
        return hash('sha256', $plainValue);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function hash(): string
    {
        return $this->hash;
    }
}
