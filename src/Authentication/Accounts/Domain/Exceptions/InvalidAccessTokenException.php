<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class InvalidAccessTokenException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Invalid access token');
    }

    public static function errorCode(): string
    {
        return 'ACC-EMAI-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
