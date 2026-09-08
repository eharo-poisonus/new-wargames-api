<?php

namespace App\Authentication\Accounts\Application\CreateAccount;

use App\Authentication\Accounts\Domain\Account;
use App\Authentication\Accounts\Domain\AccountRepository;
use App\Authentication\Accounts\Domain\Exceptions\EmailAlreadyInUseException;
use App\Authentication\Accounts\Domain\Exceptions\ReferralUserNotExistsException;
use App\Authentication\Accounts\Domain\Exceptions\UsernameAlreadyInUseException;
use App\Authentication\Accounts\Domain\ValidationTokenGenerator;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Accounts\Domain\ValueObjects\AccountType;
use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Authentication\Accounts\Domain\ValueObjects\HashedPassword;
use App\Authentication\Accounts\Domain\ValueObjects\PlainPassword;
use App\Authentication\Accounts\Domain\ValueObjects\TermsVersion;
use App\Authentication\Accounts\Domain\ValueObjects\Username;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filters\Filter;
use App\Shared\Domain\Criteria\Groups\FiltersGroupAnd;

final readonly class AccountCreatorService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private ValidationTokenGenerator $validationTokenGenerator,
        private EventBus $syncEventBus
    ) {
    }

    /** @throws EmailAlreadyInUseException | UsernameAlreadyInUseException | ReferralUserNotExistsException */
    public function __invoke(
        AccountId $id,
        AccountType $type,
        Username $username,
        Email $email,
        PlainPassword $password,
        ?AccountId $referredBy,
        bool $emailMarketingAccepted,
        bool $termsAccepted,
        TermsVersion $termsVersion
    ): void {
        $this->ensureUsernameNotInUse($username);
        $this->ensureEmailNotInUse($email);
        $this->ensureReferredByAccountExists($referredBy);

        $validationToken = $this->validationTokenGenerator->generate();

        $newAccount = Account::create(
            $id,
            $type,
            $username,
            $email,
            HashedPassword::fromString($password->hash()),
            $referredBy,
            $emailMarketingAccepted,
            $termsAccepted,
            $termsVersion,
            $this->validationTokenGenerator->hash($validationToken),
            $validationToken
        );

        $this->accountRepository->save($newAccount);

        $this->syncEventBus->publish(...$newAccount->pullDomainEvents());
    }

    /** @throws UsernameAlreadyInUseException */
    private function ensureUsernameNotInUse(Username $username): void
    {
        $otherAccountsWithSameUsername = $this->accountRepository->search(
            Criteria::create([
                FiltersGroupAnd::fromValues([
                    Filter::fromValues([
                        'field' => 'username',
                        'operator' => '=',
                        'value' => $username
                    ])
                ])
            ])
        );

        if (!empty($otherAccountsWithSameUsername)) {
            throw new UsernameAlreadyInUseException();
        }
    }

    /** @throws EmailAlreadyInUseException */
    private function ensureEmailNotInUse(Email $email): void
    {
        $otherAccountsWithSameEmail = $this->accountRepository->search(
            Criteria::create([
                FiltersGroupAnd::fromValues([
                    Filter::fromValues([
                        'field' => 'email',
                        'operator' => '=',
                        'value' => $email
                    ])
                ])
            ])
        );

        if (!empty($otherAccountsWithSameEmail)) {
            throw new EmailAlreadyInUseException();
        }
    }

    /** @throws ReferralUserNotExistsException */
    private function ensureReferredByAccountExists(?AccountId $referredBy): void
    {
        if ($referredBy === null) {
            return;
        }

        $referrer = $this->accountRepository->id($referredBy);

        if (null === $referrer) {
            throw new ReferralUserNotExistsException();
        }
    }
}
