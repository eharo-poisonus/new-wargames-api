<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class AccountNotVerifiedException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Account not verified');
    }

    public static function errorCode(): string
    {
        return 'ACC-ACC-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
