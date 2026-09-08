<?php

namespace App\Shared\Domain\Exception;

use Exception;

abstract class BaseException extends Exception implements MappedException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message, static::httpStatusCode());
    }
}
