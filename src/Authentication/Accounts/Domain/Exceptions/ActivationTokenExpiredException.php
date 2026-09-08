<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class ActivationTokenExpiredException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Activation token expired');
    }

    public static function errorCode(): string
    {
        return 'ACC-ACT-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
