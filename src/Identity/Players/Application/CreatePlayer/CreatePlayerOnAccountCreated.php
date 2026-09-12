<?php

namespace App\Identity\Players\Application\CreatePlayer;

use App\Authentication\Accounts\Domain\Events\AccountCreated;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Identity\Players\Domain\ValueObjects\PlayerId;
use App\Identity\Players\Domain\ValueObjects\Username;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class CreatePlayerOnAccountCreated implements DomainEventSubscriber
{
    public function __construct(
        private PlayerCreatorService $service
    ) {
    }

    public function __invoke(AccountCreated $event): void
    {
        ($this->service)(
            PlayerId::random(),
            AccountId::fromString($event->aggregateId()),
            Username::fromString($event->username())
        );
    }

    public static function subscribedTo(): array
    {
        return [AccountCreated::class];
    }
}
