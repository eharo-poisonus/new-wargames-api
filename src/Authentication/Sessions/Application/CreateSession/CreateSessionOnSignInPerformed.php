<?php

namespace App\Authentication\Sessions\Application\CreateSession;

use App\Authentication\Accounts\Domain\Events\SignedIn;
use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class CreateSessionOnSignInPerformed implements DomainEventSubscriber
{
    public function __construct(
        private SessionCreatorService $service
    ) {
    }

    /** @throws AccountDoesNotExistException */
    public function __invoke(SignedIn $event): void
    {
        ($this->service)(
            AccountId::fromString($event->aggregateId()),
            SessionId::fromString($event->sessionId()),
            $event->device(),
            $event->ipAddress(),
            $event->hashedRefreshToken()
        );
    }

    public static function subscribedTo(): array
    {
        return [SignedIn::class];
    }
}
