<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class InvalidPasswordException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Invalid password');
    }

    public static function errorCode(): string
    {
        return 'APP-PASS-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
