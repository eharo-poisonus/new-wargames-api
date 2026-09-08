<?php

namespace App\Authentication\Sessions\Application\CreateSession;

use App\Authentication\Accounts\Domain\Account;
use App\Authentication\Accounts\Domain\AccountRepository;
use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Sessions\Domain\Session;
use App\Authentication\Sessions\Domain\SessionRepository;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class SessionCreatorService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private SessionRepository $sessionRepository,
        private EventBus $syncEventBus,
        private int $refreshTokenTtl
    ) {
    }

    /** @throws AccountDoesNotExistException */
    public function __invoke(
        AccountId $accountId,
        SessionId $sessionId,
        string $device,
        string $ipAddress,
        string $refreshTokenHash
    ): void {
        $account = $this->accountRepository->id($accountId);
        $this->ensureAccountExists($account);

        $session = Session::create(
            $sessionId,
            $accountId,
            $device,
            $ipAddress,
            $this->refreshTokenTtl,
            $refreshTokenHash
        );

        $this->sessionRepository->save($session);

        $this->syncEventBus->publish(...$session->pullDomainEvents());
    }

    /** @throws AccountDoesNotExistException */
    private function ensureAccountExists(?Account $account): void
    {
        if (null === $account) {
            throw new AccountDoesNotExistException();
        }
    }
}
