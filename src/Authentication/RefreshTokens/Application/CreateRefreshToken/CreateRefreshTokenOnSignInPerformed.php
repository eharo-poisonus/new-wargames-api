<?php

namespace App\Authentication\RefreshTokens\Application\CreateRefreshToken;

use App\Authentication\Sessions\Domain\Events\SessionCreated;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class CreateRefreshTokenOnSignInPerformed implements DomainEventSubscriber
{
    public function __construct(
        private RefreshTokenCreatorService $service,
        private int $refreshTokenTtl
    ) {
    }

    public function __invoke(SessionCreated $event): void
    {
        ($this->service)(
            SessionId::fromString($event->aggregateId()),
            $event->refreshTokenHash(),
            $this->refreshTokenTtl
        );
    }

    public static function subscribedTo(): array
    {
        return [SessionCreated::class];
    }
}
