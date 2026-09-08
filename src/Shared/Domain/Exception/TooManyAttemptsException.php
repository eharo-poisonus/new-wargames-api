<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class TooManyAttemptsException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Too many attempts. Wait a little and try again.');
    }

    public static function errorCode(): string
    {
        return 'APP-002';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_TOO_MANY_REQUESTS;
    }
}
