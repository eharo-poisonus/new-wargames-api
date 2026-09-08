<?php

namespace App\Authentication\RefreshTokens\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class InvalidRefreshTokenException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Invalid refresh token');
    }

    public static function errorCode(): string
    {
        return 'APP-RTKN-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
