<?php

namespace App\Authentication\RefreshTokens\Application\RefreshRefreshToken;

use App\Authentication\Accounts\Domain\Exceptions\AccountDeletedException;
use App\Authentication\Accounts\Domain\Exceptions\AccountDoesNotExistException;
use App\Authentication\Accounts\Domain\Exceptions\AccountNotActivatedException;
use App\Authentication\RefreshTokens\Domain\Exceptions\InvalidRefreshTokenException;
use App\Authentication\RefreshTokens\Domain\Exceptions\RefreshTokenExpiredException;
use App\Authentication\RefreshTokens\Domain\Exceptions\RefreshTokenRevokedException;
use App\Authentication\Sessions\Domain\Exceptions\SessionDoesNotExistException;
use App\Authentication\Sessions\Domain\Exceptions\SessionExpiredException;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Bus\Query\QueryHandler;

final readonly class RefreshTokenQueryHandler implements QueryHandler
{
    public function __construct(
        private RefreshTokenRefresherService $service
    ) {
    }

    /**
     * @throws InvalidRefreshTokenException|RefreshTokenExpiredException|RefreshTokenRevokedException
     * @throws SessionDoesNotExistException|SessionExpiredException|AccountDoesNotExistException
     * @throws AccountNotActivatedException|AccountDeletedException
     */
    public function __invoke(RefreshTokenQuery $query): RefreshTokenResponse
    {
        return ($this->service)(SessionId::fromString($query->sessionId()), $query->refreshToken());
    }
}
