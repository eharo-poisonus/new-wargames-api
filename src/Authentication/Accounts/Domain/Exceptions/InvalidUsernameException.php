<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class InvalidUsernameException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Invalid username');
    }

    public static function errorCode(): string
    {
        return 'APP-USER-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
