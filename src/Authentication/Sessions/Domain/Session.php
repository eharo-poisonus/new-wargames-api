<?php

namespace App\Authentication\Sessions\Domain;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Sessions\Domain\Events\SessionCreated;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;

class Session extends AggregateRoot
{
    public function __construct(
        private SessionId $id,
        private AccountId $accountId,
        private string $device,
        private string $ipAddress,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $expiresAt
    ) {
    }

    public static function create(
        SessionId $id,
        AccountId $accountId,
        string $device,
        string $ipAddress,
        int $ttl,
        string $refreshTokenHash
    ): self {
        $session = new self(
            $id,
            $accountId,
            $device,
            $ipAddress,
            new DateTimeImmutable(),
            new DateTimeImmutable(sprintf('+%s seconds', $ttl))
        );

        $session->record(new SessionCreated($session->id, $refreshTokenHash));

        return $session;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt <= new DateTimeImmutable();
    }

    public function id(): SessionId
    {
        return $this->id;
    }

    public function setId(SessionId $id): void
    {
        $this->id = $id;
    }

    public function accountId(): AccountId
    {
        return $this->accountId;
    }

    public function setAccountId(AccountId $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function device(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): void
    {
        $this->device = $device;
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
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
}
