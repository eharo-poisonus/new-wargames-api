<?php

namespace App\Authentication\Accounts\Domain;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Authentication\Accounts\Domain\ValueObjects\Username;

readonly class AccessTokenClaims
{
    public function __construct(
        private AccountId $sub,
        private Username $username,
        private Email $email,
        private array $roles
    ) {
    }

    public function sub(): AccountId
    {
        return $this->sub;
    }

    public function username(): Username
    {
        return $this->username;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function roles(): array
    {
        return $this->roles;
    }
}
