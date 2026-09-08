<?php

namespace App\Authentication\RefreshTokens\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenExpiredException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Refresh token expired');
    }

    public static function errorCode(): string
    {
        return 'APP-RTKN-002';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
