<?php

namespace App\Authentication\Accounts\Application\CreateAccount;

use App\Authentication\Accounts\Domain\Exceptions\EmailAlreadyInUseException;
use App\Authentication\Accounts\Domain\Exceptions\ReferralUserNotExistsException;
use App\Authentication\Accounts\Domain\Exceptions\UsernameAlreadyInUseException;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Accounts\Domain\ValueObjects\AccountType;
use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Authentication\Accounts\Domain\ValueObjects\PlainPassword;
use App\Authentication\Accounts\Domain\ValueObjects\TermsVersion;
use App\Identity\Players\Domain\ValueObjects\Username;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class CreateAccountCommandHandler implements CommandHandler
{
    public function __construct(
        private AccountCreatorService $service
    ) {
    }

    /** @throws EmailAlreadyInUseException|UsernameAlreadyInUseException|ReferralUserNotExistsException */
    public function __invoke(CreateAccountCommand $command): void
    {
        ($this->service)(
            AccountId::fromString($command->id()),
            AccountType::from($command->type()),
            Email::fromString($command->email()),
            Username::fromString($command->username()),
            PlainPassword::fromString($command->password()),
            $command->referredBy() === null ? null : AccountId::fromString($command->referredBy()),
            $command->isEmailMarketingAccepted(),
            $command->isTermsAccepted(),
            TermsVersion::from($command->termsVersion())
        );
    }
}
