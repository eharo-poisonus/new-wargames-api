<?php

namespace App\Authentication\Accounts\Domain\ValueObjects;

enum AccountType: string
{
    case PERSONAL = 'PERSONAL';
    case BUSINESS = 'BUSINESS';
    case CLUB = 'CLUB';
    case SUPPLIER = 'SUPPLIER';
    case PARTNER = 'PARTNER';
}
