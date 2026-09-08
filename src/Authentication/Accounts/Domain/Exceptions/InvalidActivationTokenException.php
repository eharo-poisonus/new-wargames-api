<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class InvalidActivationTokenException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Invalid activation token');
    }

    public static function errorCode(): string
    {
        return 'ACC-ACT-002';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
