<?php

namespace App\Communication\AccountValidation\Application\SendEmail;

use App\Authentication\Accounts\Domain\Events\AccountCreated;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Identity\Players\Domain\ValueObjects\Username;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;
use App\Shared\Domain\Bus\Event\InMemoryDomainEventSubscriber;

final readonly class SendValidationEmailOnAccountCreated implements DomainEventSubscriber
{
    public function __construct(
        private ValidationEmailSenderService $service
    ) {
    }

    public function __invoke(AccountCreated $event): void
    {
        ($this->service)(
            AccountId::fromString($event->aggregateId()),
            $event->validationToken(),
            Username::fromString($event->username())
        );
    }

    public static function subscribedTo(): array
    {
        return [AccountCreated::class];
    }
}
