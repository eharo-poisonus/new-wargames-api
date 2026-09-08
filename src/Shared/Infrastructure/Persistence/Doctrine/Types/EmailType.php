<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Types;

use App\Shared\Domain\Utils;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCustomType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

use function Lambdish\Phunctional\last;

abstract class EmailType extends StringType implements DoctrineCustomType
{
    abstract protected function typeClassName(): string;

    final public static function customTypeName(): string
    {
        return Utils::toSnakeCase(
            str_replace('Type', '', (string)last(explode('\\', static::class)))
        );
    }

    final public function getName(): string
    {
        return self::customTypeName();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        $className = $this->typeClassName();

        return $className::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value?->value();
    }
}
