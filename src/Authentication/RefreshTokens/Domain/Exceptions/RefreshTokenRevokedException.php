<?php

namespace App\Authentication\RefreshTokens\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenRevokedException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Refresh token revoked');
    }

    public static function errorCode(): string
    {
        return 'APP-RTKN-003';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
