<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class ReferralUserNotExistsException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Referrer account does not exist');
    }

    public static function errorCode(): string
    {
        return 'ACC-REFE-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
