<?php

namespace App\Authentication\RefreshTokens\Application\RevokeRefreshTokens;

use App\Authentication\RefreshTokens\Domain\RefreshTokenRepository;
use App\Authentication\Sessions\Domain\ValueObjects\SessionId;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filters\Filter;
use App\Shared\Domain\Criteria\Groups\FiltersGroupAnd;

final readonly class RefreshTokensRevokerService
{
    public function __construct(
        private RefreshTokenRepository $refreshTokenRepository
    ) {
    }

    public function __invoke(SessionId $sessionId): void
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
}
