<?php

namespace App\Authentication\RefreshTokens\Application\RefreshRefreshToken;

use App\Authentication\Accounts\Domain\AccessTokenClaims;
use App\Authentication\Accounts\Domain\AccessTokenUtils;
use App\Authentication\Accounts\Domain\Account;
use App\Authentication\Accounts\Domain\AccountRepository;
use App\Authentication\Accounts\Domain\Exceptions\AccountDeletedException;
use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Accounts\Domain\Exceptions\AccountNotActivatedException;
use App\Authentication\RefreshTokens\Application\CreateRefreshToken\RefreshTokenCreatorService;
use App\Authentication\RefreshTokens\Domain\Exceptions\InvalidRefreshTokenException;
use App\Authentication\RefreshTokens\Domain\Exceptions\RefreshTokenExpiredException;
use App\Authentication\RefreshTokens\Domain\Exceptions\RefreshTokenRevokedException;
use App\Authentication\RefreshTokens\Domain\RefreshToken;
use App\Authentication\RefreshTokens\Domain\RefreshTokenGenerator;
use App\Authentication\RefreshTokens\Domain\RefreshTokenHasher;
use App\Authentication\RefreshTokens\Domain\RefreshTokenRepository;
use App\Authentication\Sessions\Domain\Exceptions\SessionDoesNotExistException;
use App\Authentication\Sessions\Domain\Exceptions\SessionExpiredException;
use App\Authentication\Sessions\Domain\Session;
use App\Authentication\Sessions\Domain\SessionRepository;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filters\Filter;
use App\Shared\Domain\Criteria\Groups\FiltersGroupAnd;

final readonly class RefreshTokenRefresherService
{
    public function __construct(
        private RefreshTokenRepository $refreshTokenRepository,
        private SessionRepository $sessionRepository,
        private AccountRepository $accountRepository,
        private RefreshTokenHasher $refreshTokenHasher,
        private RefreshTokenGenerator $refreshTokenGenerator,
        private RefreshTokenCreatorService $refreshTokenCreator,
        private AccessTokenUtils $accessTokenUtils,
        private int $refreshTokenTtl
    ) {
    }

    /**
     * @throws SessionDoesNotExistException|SessionExpiredException|InvalidRefreshTokenException
     * @throws RefreshTokenExpiredException|RefreshTokenRevokedException|AccountDoesNotExistException
     * @throws AccountNotActivatedException|AccountDeletedException
     */
    public function __invoke(SessionId $sessionId, string $plainRefreshToken): RefreshTokenResponse
    {
        $session = $this->sessionRepository->id($sessionId);
        $this->ensureSessionExists($session);
        $this->ensureSessionIsUsable($session);

        $refreshToken = $this->retrieveRefreshToken($plainRefreshToken);
        $this->ensureRefreshTokenBelongsToSession($refreshToken, $session);
        $this->ensureRefreshTokenIsUsable($refreshToken);

        $account = $this->accountRepository->id($session->accountId());
        $this->ensureAccountExists($account);
        $this->ensureAccountCanRefreshToken($account);

        $refreshToken->revoke();
        $this->refreshTokenRepository->update($refreshToken);

        $rotatedRefreshToken = $this->refreshTokenGenerator->generate();

        ($this->refreshTokenCreator)(
            $session->id(),
            $this->refreshTokenHasher->hash($rotatedRefreshToken),
            $this->refreshTokenTtl
        );

        return new RefreshTokenResponse(
            $this->accessTokenUtils->generate($this->createClaims($account)),
            $rotatedRefreshToken,
            $session->id()->value()
        );
    }

    /** @throws SessionDoesNotExistException */
    private function ensureSessionExists(?Session $session): void
    {
        if (null === $session) {
            throw new SessionDoesNotExistException();
        }
    }

    /** @throws SessionExpiredException */
    private function ensureSessionIsUsable(Session $session): void
    {
        if ($session->isExpired()) {
            throw new SessionExpiredException();
        }
    }

    /** @throws InvalidRefreshTokenException */
    private function retrieveRefreshToken(string $plainRefreshToken): RefreshToken
    {
        $refreshTokens = $this->refreshTokenRepository->search(
            Criteria::create([
                FiltersGroupAnd::fromValues([
                    Filter::fromValues([
                        'field' => 'hashedToken',
                        'operator' => '=',
                        'value' => $this->refreshTokenHasher->hash($plainRefreshToken)
                    ])
                ])
            ])
        );

        if (count($refreshTokens) !== 1) {
            throw new InvalidRefreshTokenException();
        }

        return array_shift($refreshTokens);
    }

    /** @throws InvalidRefreshTokenException */
    private function ensureRefreshTokenBelongsToSession(RefreshToken $refreshToken, Session $session): void
    {
        if (!$refreshToken->sessionId()->equals($session->id())) {
            throw new InvalidRefreshTokenException();
        }
    }

    /** @throws RefreshTokenRevokedException|RefreshTokenExpiredException */
    private function ensureRefreshTokenIsUsable(RefreshToken $refreshToken): void
    {
        if ($refreshToken->isRevoked()) {
            $this->revokeSessionRefreshTokens($refreshToken->sessionId());

            throw new RefreshTokenRevokedException();
        }

        if ($refreshToken->isExpired()) {
            throw new RefreshTokenExpiredException();
        }
    }

    private function revokeSessionRefreshTokens(SessionId $sessionId): void
    {
        $refreshTokens = $this->refreshTokenRepository->search(
            Criteria::create([
                FiltersGroupAnd::fromValues([
                    Filter::fromValues([
                        'field' => 'sessionId',
                        'operator' => '=',
                        'value' => $sessionId
                    ]),
                    Filter::fromValues([
                        'field' => 'revokedAt',
                        'operator' => '=',
                        'value' => null
                    ])
                ])
            ])
        );

        foreach ($refreshTokens as $refreshToken) {
            $refreshToken->revoke();
            $this->refreshTokenRepository->update($refreshToken);
        }
    }

    /** @throws AccountDoesNotExistException */
    private function ensureAccountExists(?Account $account): void
    {
        if (null === $account) {
            throw new AccountDoesNotExistException();
        }
    }

    /** @throws AccountNotActivatedException|AccountDeletedException */
    private function ensureAccountCanRefreshToken(Account $account): void
    {
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
