<?php

namespace App\Authentication\Sessions\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class SessionTerminatedException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Session terminated');
    }

    public static function errorCode(): string
    {
        return 'APP-SESS-003';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
