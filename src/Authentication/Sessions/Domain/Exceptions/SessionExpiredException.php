<?php

namespace App\Authentication\Sessions\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class SessionExpiredException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Session expired');
    }

    public static function errorCode(): string
    {
        return 'APP-SESS-002';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
