<?php

namespace App\Shared\Domain;

enum Auth: string
{
    case FREE = 'FREE';
    case JWT = 'JWT';
}
