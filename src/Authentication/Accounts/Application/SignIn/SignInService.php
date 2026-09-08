<?php

namespace App\Authentication\Accounts\Application\SignIn;

use App\Authentication\Accounts\Domain\AccessTokenClaims;
use App\Authentication\Accounts\Domain\AccessTokenUtils;
use App\Authentication\Accounts\Domain\Account;
use App\Authentication\Accounts\Domain\AccountRepository;
use App\Authentication\Accounts\Domain\Exceptions\AccountDeletedException;
use App\Authentication\Accounts\Domain\Exceptions\AccountNotActivatedException;
use App\Authentication\Accounts\Domain\Exceptions\AccountNotVerifiedException;
use App\Authentication\Accounts\Domain\Exceptions\InvalidPasswordException;
use App\Authentication\Accounts\Domain\Exceptions\MoreThanOneAccountWithSameEmailException;
use App\Authentication\Accounts\Domain\Exceptions\TermsNotAcceptedException;
use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Authentication\Accounts\Domain\ValueObjects\PlainPassword;
use App\Authentication\RefreshTokens\Domain\RefreshTokenGenerator;
use App\Authentication\RefreshTokens\Domain\RefreshTokenHasher;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filters\Filter;
use App\Shared\Domain\Criteria\Groups\FiltersGroupAnd;

final readonly class SignInService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private RefreshTokenGenerator $refreshTokenGenerator,
        private RefreshTokenHasher $refreshTokenHasher,
        private AccessTokenUtils $accessTokenUtils,
        private EventBus $syncEventBus
    ) {
    }

    /**
     * @throws TermsNotAcceptedException|AccountNotVerifiedException|AccountDeletedException
     * @throws MoreThanOneAccountWithSameEmailException|AccountNotActivatedException|InvalidPasswordException
     */
    public function __invoke(Email $email, PlainPassword $plainPassword, string $device, string $ipAddress): SignInResponse
    {
        $account = $this->retrieveAccountFromEmail($email);
        $this->ensurePasswordIsCorrect($account, $plainPassword);
        $this->ensureAccountCanPerformSignIn($account);

        $sessionId = SessionId::random();
        $refreshToken = $this->refreshTokenGenerator->generate();

        $claims = $this->createClaims($account);
        $accessToken = $this->accessTokenUtils->generate($claims);

        $account->signedIn(
            $sessionId,
            $device,
            $ipAddress,
            $this->refreshTokenHasher->hash($refreshToken)
        );

        $this->syncEventBus->publish(...$account->pullDomainEvents());

        return new SignInResponse($accessToken, $refreshToken, $sessionId);
    }

    /** @throws MoreThanOneAccountWithSameEmailException */
    private function retrieveAccountFromEmail(Email $email): ?Account
    {
        $accounts = $this->accountRepository->search(
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

        if (count($accounts) !== 1) {
            throw new MoreThanOneAccountWithSameEmailException();
        }

        return array_shift($accounts);
    }

    private function ensurePasswordIsCorrect(Account $account, PlainPassword $plainPassword): void
    {
        if (!$account->password()->verify($plainPassword)) {
            throw new InvalidPasswordException();
        }
    }

    /**
     * @throws TermsNotAcceptedException|AccountNotVerifiedException
     * @throws AccountDeletedException|AccountNotActivatedException
     */
    private function ensureAccountCanPerformSignIn(Account $account): void
    {
        if (!$account->isTermsAccepted()) {
            throw new TermsNotAcceptedException();
        }

        if (!$account->isVerified()) {
            throw new AccountNotVerifiedException();
        }

        if (null === $account->activatedAt()) {
            throw new AccountNotActivatedException();
        }

        if (null !== $account->deletedAt()) {
            throw new AccountDeletedException();
        }
    }

    private function createClaims(Account $account): AccessTokenClaims
    {
        return new AccessTokenClaims(
            $account->id(),
            $account->username(),
            $account->email(),
            []
        );
    }
}
