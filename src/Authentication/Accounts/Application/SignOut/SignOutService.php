<?php

namespace App\Authentication\Accounts\Application\SignOut;

use App\Authentication\Accounts\Domain\AccessTokenUtils;
use App\Authentication\Accounts\Domain\Account;
use App\Authentication\Accounts\Domain\AccountRepository;
use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Sessions\Domain\Exceptions\SessionDoesNotExistException;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class SignOutService
{
    public function __construct(
        private AccessTokenUtils $accessTokenUtils,
        private AccountRepository $accountRepository,
        private EventBus $syncEventBus
    ) {
    }

    /** @throws AccountDoesNotExistException */
    public function __invoke(SessionId $sessionId, string $accessToken): void
    {
        $claims = $this->accessTokenUtils->retrieveClaims($accessToken);

        $account = $this->accountRepository->id($claims->sub());
        $this->ensureAccountExists($account);

        $account->signOut($sessionId);

        $this->syncEventBus->publish(...$account->pullDomainEvents());
    }

    /** @throws AccountDoesNotExistException */
    private function ensureAccountExists(?Account $account): void
    {
        if (null === $account) {
            throw new AccountDoesNotExistException();
        }
    }
}
