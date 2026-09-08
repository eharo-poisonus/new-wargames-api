<?php

namespace App\Communication\Emails\Domain\Exceptions;

use App\Shared\Domain\Exception\BaseException;
use Symfony\Component\HttpFoundation\Response;

class EmailDeliveryFailedException extends BaseException
{
    public function __construct()
    {
        parent::__construct('The email could not be delivered. Please try again later.');
    }

    public static function errorCode(): string
    {
        return 'COMM-EML-002';
    }

    public static function httpStatusCode(): int
    {
        return Response::HTTP_BAD_GATEWAY;
    }
}
