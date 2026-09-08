<?php

namespace App\Authentication\Accounts\Application\SignOut;

use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Sessions\Domain\Exceptions\SessionDoesNotExistException;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class SignOutCommandHandler implements CommandHandler
{
    public function __construct(
        private SignOutService $service
    ) {
    }

    /** @throws AccountDoesNotExistException|SessionDoesNotExistException */
    public function __invoke(SignOutCommand $command): void
    {
        ($this->service)(
            SessionId::fromString($command->sessionId()),
            $command->accessToken()
        );
    }
}
