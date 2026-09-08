<?php

namespace App\Authentication\Accounts\Infrastructure\Security;

use App\Authentication\Accounts\Domain\AccessTokenClaims;
use App\Authentication\Accounts\Domain\AccessTokenUtils;
use App\Authentication\Accounts\Domain\Exceptions\InvalidAccessTokenException;
use App\Authentication\Accounts\Domain\ValueObjects\AccountId;
use App\Authentication\Accounts\Domain\ValueObjects\Email;
use App\Authentication\Accounts\Domain\ValueObjects\Username;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ramsey\Uuid\Uuid;
use Throwable;

final readonly class JwtAccessTokenUtils implements AccessTokenUtils
{
    public function __construct(
        private string $jwtSecret,
        private string $jwtAlgorithm,
        private int $accessTokenTtl
    ) {
    }

    public function generate(AccessTokenClaims $claims): string
    {
        $issuedAt = time();

        $payload = [
            'jti' => Uuid::uuid4()->toString(),
            'sub' => $claims->sub()->value(),
            'username' => $claims->username()->value(),
            'email' => $claims->email()->value(),
            'roles' => $claims->roles(),
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->accessTokenTtl
        ];

        return JWT::encode($payload, $this->jwtSecret, $this->jwtAlgorithm);
    }

    public function verify(string $token): bool
    {
        try {
            JWT::decode($token, new Key($this->jwtSecret, $this->jwtAlgorithm));

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function retrieveClaims(string $token): AccessTokenClaims
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, $this->jwtAlgorithm));

            return new AccessTokenClaims(
                AccountId::fromString($decoded->sub),
                Username::fromString($decoded->username),
                Email::fromString($decoded->email),
                $decoded->roles
            );
        } catch (Throwable) {
            throw new InvalidAccessTokenException();
        }
    }
}
