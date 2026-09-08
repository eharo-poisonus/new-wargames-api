<?php

namespace App\Authentication\Accounts\Application\CreateAccount;

use App\Shared\Domain\Bus\Command\Command;

readonly class CreateAccountCommand implements Command
{
    public function __construct(
        private string $id,
        private string $type,
        private string $username,
        private string $email,
        private string $password,
        private ?string $referredBy,
        private bool $emailMarketingAccepted,
        private bool $termsAccepted,
        private int $termsVersion
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function termsVersion(): int
    {
        return $this->termsVersion;
    }

    public function isTermsAccepted(): bool
    {
        return $this->termsAccepted;
    }

    public function isEmailMarketingAccepted(): bool
    {
        return $this->emailMarketingAccepted;
    }

    public function referredBy(): ?string
    {
        return $this->referredBy;
    }
}
