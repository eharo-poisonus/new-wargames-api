<?php

namespace App\Authentication\RefreshTokens\Application\RevokeRefreshTokens;

use App\Authentication\Sessions\Domain\Events\SessionTerminated;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class RevokeRefreshTokensOnSessionTerminated implements DomainEventSubscriber
{
    public function __construct(
        private RefreshTokensRevokerService $service
    ) {
    }

    public function __invoke(SessionTerminated $event): void
    {
        ($this->service)(SessionId::fromString($event->aggregateId()));
    }

    public static function subscribedTo(): array
    {
        return [SessionTerminated::class];
    }
}
