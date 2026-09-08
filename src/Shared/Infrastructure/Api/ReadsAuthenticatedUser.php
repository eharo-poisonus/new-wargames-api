<?php

namespace App\Shared\Infrastructure\Api;

use App\Platform\Authentication\Domain\ValueObjects\UserCredentialsId;
use App\Platform\Authentication\Infrastructure\Security\RequestAccessTokenClaims;
use Symfony\Contracts\Service\Attribute\Required;

trait ReadsAuthenticatedUser
{
    private RequestAccessTokenClaims $requestAccessTokenClaims;

    #[Required]
    public function setRequestAccessTokenClaims(RequestAccessTokenClaims $claims): void
    {
        $this->requestAccessTokenClaims = $claims;
    }

    private function authenticatedUserId(): UserCredentialsId
    {
        return $this->requestAccessTokenClaims->get()->subjectId();
    }

    private function authenticatedUsername(): string
    {
        return $this->requestAccessTokenClaims->get()->username();
    }
}
