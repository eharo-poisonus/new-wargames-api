<?php

namespace App\Authentication\Accounts\Application\ActivateAccount;

use App\Shared\Domain\Bus\Command\Command;

readonly class ActivateAccountCommand implements Command
{
    public function __construct(
        private string $accountId,
        private string $token
    ) {
    }

    public function accountId(): string
    {
        return $this->accountId;
    }

    public function token(): string
    {
        return $this->token;
    }
}
