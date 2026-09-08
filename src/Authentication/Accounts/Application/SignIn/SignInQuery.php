<?php

namespace App\Authentication\Accounts\Application\SignIn;

use App\Shared\Domain\Bus\Query\Query;

readonly class SignInQuery implements Query
{
    public function __construct(
        private string $email,
        private string $password,
        private string $device,
        private string $ipAddress
    ) {
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function device(): string
    {
        return $this->device;
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }
}
