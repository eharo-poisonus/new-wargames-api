<?php

namespace App\Authentication\Accounts\Application\ActivateAccount;

use App\Authentication\Accounts\Domain\Account;
use App\Authentication\Accounts\Domain\AccountRepository;
use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Accounts\Domain\Exceptions\ActivationTokenExpiredException;
use App\Authentication\Accounts\Domain\Exceptions\InvalidActivationTokenException;
use App\Authentication\Accounts\Domain\ValidationTokenGenerator;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use DateTimeImmutable;

final readonly class AccountActivatorService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private ValidationTokenGenerator $tokenGenerator
    ) {
    }

    /** @throws AccountDoesNotExistException | ActivationTokenExpiredException | InvalidActivationTokenException */
    public function __invoke(AccountId $id, string $activationToken): void
    {
        $account = $this->accountRepository->id($id);

        $this->ensureAccountExists($account);
        $this->ensureActivationTokenExpired($account);
        $this->ensureActivationTokenIsCorrect($activationToken, $account->activationToken());

        $account->activate();

        $this->accountRepository->update($account);
    }

    /** @throws AccountDoesNotExistException */
    private function ensureAccountExists(?Account $account): void
    {
        if (null === $account) {
            throw new AccountDoesNotExistException();
        }
    }

    /** @throws ActivationTokenExpiredException */
    private function ensureActivationTokenExpired(Account $account): void
    {
        if ($account->activationTokenExpiresAt() < new DateTimeImmutable()) {
            throw new ActivationTokenExpiredException();
        }
    }

    /** @throws InvalidActivationTokenException */
    private function ensureActivationTokenIsCorrect(string $activationToken, string $hashedActivationToken): void
    {
        if (!$this->tokenGenerator->verify($activationToken, $hashedActivationToken)) {
            throw new InvalidActivationTokenException();
        }
    }
}
