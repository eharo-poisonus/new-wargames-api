<?php

namespace App\Authentication\Accounts\Domain;

use App\Authentication\Accounts\Domain\Events\AccountCreated;
use App\Authentication\Accounts\Domain\Events\SignedIn;
use App\Authentication\Accounts\Domain\Events\SignedOut;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Accounts\Domain\ValueObjects\AccountType;
use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Authentication\Accounts\Domain\ValueObjects\HashedPassword;
use App\Authentication\Accounts\Domain\ValueObjects\TermsVersion;
use App\Authentication\Accounts\Domain\ValueObjects\Username;
use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;

class Account extends AggregateRoot
{
    private function __construct(
        private AccountId $id,
        private AccountType $type,
        private Username $username,
        private Email $email,
        private HashedPassword $password,
        private bool $verified,
        private ?AccountId $referredBy,
        private bool $emailMarketingAccepted,
        private bool $termsAccepted,
        private TermsVersion $termsVersion,
        private ?string $activationToken,
        private ?DateTimeImmutable $activationTokenExpiresAt,
        private DateTimeImmutable $termsAcceptedAt,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt,
        private ?DateTimeImmutable $activatedAt
    ) {
    }

    public static function create(
        AccountId $id,
        AccountType $type,
        Username $username,
        Email $email,
        HashedPassword $password,
        ?AccountId $referredBy,
        bool $emailMarketingAccepted,
        bool $termsAccepted,
        TermsVersion $termsVersion,
        string $hashedActivationToken,
        string $plainActivationToken
    ): self {
        $account = new self(
            $id,
            $type,
            $username,
            $email,
            $password,
            $type === AccountType::PERSONAL,
            $referredBy,
            $emailMarketingAccepted,
            $termsAccepted,
            $termsVersion,
            $hashedActivationToken,
            new DateTimeImmutable('+1 day'),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            null,
            null,
            null
        );

        $account->record(
            new AccountCreated(
                $account->id,
                $plainActivationToken
            )
        );

        return $account;
    }

    public function activate(): void
    {
        $this->activatedAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->activationToken = null;
        $this->activationTokenExpiresAt = null;
    }

    public function signIn(string $sessionId, string $device, string $ipAddress, string $hashedRefreshToken): void
    {
        $this->record(
            new SignedIn(
                $this->id,
                $sessionId,
                $device,
                $ipAddress,
                $hashedRefreshToken
            )
        );
    }

    public function signOut(string $sessionId): void
    {
        $this->record(
            new SignedOut(
                $this->id,
                $sessionId
            )
        );
    }

    public function delete(): void
    {
        $this->deletedAt = new DateTimeImmutable();
    }

    public function id(): AccountId
    {
        return $this->id;
    }

    public function setId(AccountId $id): void
    {
        $this->id = $id;
    }

    public function type(): AccountType
    {
        return $this->type;
    }

    public function setType(AccountType $type): void
    {
        $this->type = $type;
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function setUsername(Username $username): void
    {
        $this->username = $username;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }

    public function setPassword(HashedPassword $password): void
    {
        $this->password = $password;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }

    public function referredBy(): ?AccountId
    {
        return $this->referredBy;
    }

    public function setReferredBy(?AccountId $referredBy): void
    {
        $this->referredBy = $referredBy;
    }

    public function isEmailMarketingAccepted(): bool
    {
        return $this->emailMarketingAccepted;
    }

    public function setEmailMarketingAccepted(bool $emailMarketingAccepted): void
    {
        $this->emailMarketingAccepted = $emailMarketingAccepted;
    }

    public function isTermsAccepted(): bool
    {
        return $this->termsAccepted;
    }

    public function setTermsAccepted(bool $termsAccepted): void
    {
        $this->termsAccepted = $termsAccepted;
    }

    public function termsVersion(): TermsVersion
    {
        return $this->termsVersion;
    }

    public function setTermsVersion(TermsVersion $termsVersion): void
    {
        $this->termsVersion = $termsVersion;
    }

    public function activationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): void
    {
        $this->activationToken = $activationToken;
    }

    public function activationTokenExpiresAt(): ?DateTimeImmutable
    {
        return $this->activationTokenExpiresAt;
    }

    public function setActivationTokenExpiresAt(?DateTimeImmutable $activationTokenExpiresAt): void
    {
        $this->activationTokenExpiresAt = $activationTokenExpiresAt;
    }

    public function termsAcceptedAt(): DateTimeImmutable
    {
        return $this->termsAcceptedAt;
    }

    public function setTermsAcceptedAt(DateTimeImmutable $termsAcceptedAt): void
    {
        $this->termsAcceptedAt = $termsAcceptedAt;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function activatedAt(): ?DateTimeImmutable
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(?DateTimeImmutable $activatedAt): void
    {
        $this->activatedAt = $activatedAt;
    }
}
