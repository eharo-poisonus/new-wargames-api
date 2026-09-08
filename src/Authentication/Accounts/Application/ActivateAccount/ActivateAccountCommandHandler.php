<?php

namespace App\Authentication\Accounts\Application\ActivateAccount;

use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Accounts\Domain\Exceptions\ActivationTokenExpiredException;
use App\Authentication\Accounts\Domain\Exceptions\InvalidActivationTokenException;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class ActivateAccountCommandHandler implements CommandHandler
{
    public function __construct(
        private AccountActivatorService $service
    ) {
    }

    /** @throws AccountDoesNotExistException | ActivationTokenExpiredException | InvalidActivationTokenException */
    public function __invoke(ActivateAccountCommand $command): void
    {
        ($this->service)(
            AccountId::fromString($command->accountId()),
            $command->token()
        );
    }
}
