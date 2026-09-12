<?php

namespace App\Identity\Players\Domain\ValueObjects;

enum Gender: string
{
    case MALE = 'MALE';
    case FEMALE = 'FEMALE';
    case OTHER = 'OTHER';
    case UNKNOWN = 'UNKNOWN';
}
