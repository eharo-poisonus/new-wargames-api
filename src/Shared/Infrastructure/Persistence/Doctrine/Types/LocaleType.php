<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Types;

use App\Shared\Domain\ValueObject\Locale;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCustomType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class LocaleType extends StringType implements DoctrineCustomType
{
    public static function customTypeName(): string
    {
        return 'locale';
    }

    public function getName(): string
    {
        return self::customTypeName();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Locale
    {
        return $value === null ? null : Locale::fromStringOrDefault((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof Locale ? $value->value : (string) $value;
    }
}
