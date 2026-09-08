<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnsupportedLocaleException extends BaseException
{
    public function __construct()
    {
        parent::__construct('That is not a language this application speaks. Use es, en, de or fr.');
    }

    public static function errorCode(): string
    {
        return 'APP-001';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
