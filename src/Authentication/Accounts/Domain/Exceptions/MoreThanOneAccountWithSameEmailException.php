<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class MoreThanOneAccountWithSameEmailException extends BaseException
{
    public function __construct()
    {
        parent::__construct('More than one account with same email');
    }

    public static function errorCode(): string
    {
        return 'APP-ACC-101';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }
}
