<?php

namespace App\Authentication\Accounts\Application\ValidateAccount;

use App\Shared\Domain\Bus\Command\Command;

readonly class ValidateAccountCommand implements Command
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
