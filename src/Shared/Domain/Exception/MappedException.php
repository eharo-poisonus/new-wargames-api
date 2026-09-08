<?php

namespace App\Shared\Domain\Exception;

interface MappedException
{
    public static function errorCode(): string;

    public static function httpStatusCode(): int;
}
