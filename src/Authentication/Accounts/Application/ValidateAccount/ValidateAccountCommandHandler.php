<?php

namespace App\Authentication\Accounts\Application\ValidateAccount;

use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Accounts\Domain\Exceptions\ActivationTokenExpiredException;
use App\Authentication\Accounts\Domain\Exceptions\InvalidActivationTokenException;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class ValidateAccountCommandHandler implements CommandHandler
{
    public function __construct(
        private AccountValidatorService $service
    ) {
    }

    /** @throws AccountDoesNotExistException | ActivationTokenExpiredException | InvalidActivationTokenException */
    public function __invoke(ValidateAccountCommand $command): void
    {
        ($this->service)(
            AccountId::fromString($command->accountId()),
            $command->token()
        );
    }
}
