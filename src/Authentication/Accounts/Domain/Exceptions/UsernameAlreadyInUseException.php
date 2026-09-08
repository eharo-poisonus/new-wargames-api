<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class UsernameAlreadyInUseException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Username already registered');
    }

    public static function errorCode(): string
    {
        return 'ACC-USER-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }
}
