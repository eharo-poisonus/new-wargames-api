<?php

namespace App\Authentication\Accounts\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class TermsNotAcceptedException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Terms and conditions not accepted');
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
