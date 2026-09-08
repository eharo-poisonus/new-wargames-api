<?php

namespace App\Authentication\RefreshTokens\Domain;

use App\Authentication\RefreshTokens\Domain\ValueObjects\RefreshTokenId;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;

class RefreshToken extends AggregateRoot
{
    public function __construct(
        private RefreshTokenId $id,
        private SessionId $sessionId,
        private string $hashedToken,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $expiresAt,
        private ?DateTimeImmutable $revokedAt
    ) {
    }

    public static function create(
        SessionId $sessionId,
        string $hashedToken,
        int $ttl
    ): self {
        return new self(
            RefreshTokenId::random(),
            $sessionId,
            $hashedToken,
            new DateTimeImmutable(),
            new DateTimeImmutable(sprintf('+%s seconds', $ttl)),
            null
        );
    }

    public function revoke(): void
    {
        $this->revokedAt = new DateTimeImmutable();
    }

    public function isRevoked(): bool
    {
        return null !== $this->revokedAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt <= new DateTimeImmutable();
    }

    public function id(): RefreshTokenId
    {
        return $this->id;
    }

    public function setId(RefreshTokenId $id): void
    {
        $this->id = $id;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }

    public function setSessionId(SessionId $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function hashedToken(): string
    {
        return $this->hashedToken;
    }

    public function setHashedToken(string $hashedToken): void
    {
        $this->hashedToken = $hashedToken;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function revokedAt(): ?DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function setRevokedAt(?DateTimeImmutable $revokedAt): void
    {
        $this->revokedAt = $revokedAt;
    }
}
