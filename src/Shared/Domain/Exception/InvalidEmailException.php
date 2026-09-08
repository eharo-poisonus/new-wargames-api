<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class InvalidEmailException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Invalid email');
    }

    public static function errorCode(): string
    {
        return 'APP-EMAI-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
