<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Types;

use App\Shared\Domain\ValueObject\SimpleUuid;

final class SimpleUuidType extends UuidType
{
    protected function typeClassName(): string
    {
        return SimpleUuid::class;
    }
}
