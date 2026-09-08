<?php

namespace App\Authentication\Sessions\Application\TerminateSession;

use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Sessions\Domain\Exceptions\SessionDoesNotExistException;
use App\Authentication\Sessions\Domain\Session;
use App\Authentication\Sessions\Domain\SessionRepository;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class SessionTerminatorService
{
    public function __construct(
        private SessionRepository $sessionRepository,
        private EventBus $syncEventBus
    ) {
    }

    /** @throws SessionDoesNotExistException */
    public function __invoke(AccountId $accountId, SessionId $sessionId): void
    {
        $session = $this->sessionRepository->id($sessionId);
        $this->ensureSessionExists($session);
        $this->ensureSessionBelongsToAccount($session, $accountId);

        if ($session->isTerminated()) {
            return;
        }

        $session->terminate();

        $this->sessionRepository->update($session);

        $this->syncEventBus->publish(...$session->pullDomainEvents());
    }

    /** @throws SessionDoesNotExistException */
    private function ensureSessionExists(?Session $session): void
    {
        if (null === $session) {
            throw new SessionDoesNotExistException();
        }
    }

    /** @throws SessionDoesNotExistException */
    private function ensureSessionBelongsToAccount(Session $session, AccountId $accountId): void
    {
        if (!$session->accountId()->equals($accountId)) {
            throw new SessionDoesNotExistException();
        }
    }
}
