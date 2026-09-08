<?php

namespace App\Authentication\Sessions\Application\TerminateSession;

use App\Authentication\Accounts\Domain\Events\SignedOut;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Sessions\Domain\Exceptions\SessionDoesNotExistException;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class TerminateSessionOnSignedOut implements DomainEventSubscriber
{
    public function __construct(
        private SessionTerminatorService $service
    ) {
    }

    /** @throws SessionDoesNotExistException */
    public function __invoke(SignedOut $event): void
    {
        ($this->service)(
            AccountId::fromString($event->aggregateId()),
            SessionId::fromString($event->sessionId())
        );
    }

    public static function subscribedTo(): array
    {
        return [SignedOut::class];
    }
}
