<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Infrastructure\Persistence\Doctrine\Types\LocaleType;
use App\Shared\Infrastructure\Persistence\Doctrine\Types\SimpleUuidType;
use Doctrine\ORM\EntityManagerInterface;

class EntityManagerFactory
{
    public const array SHARED_CONTEXT_TYPES = [SimpleUuidType::class, LocaleType::class];

    public static function create(array $parameters, string $environment): EntityManagerInterface
    {
        $mappings = MappingSearcher::inContext(null, ['Shared'], ['Symfony']);
        $customTypes = CustomTypeSearcher::fromPaths(array_keys($mappings));
        $customTypes = array_merge($customTypes, self::SHARED_CONTEXT_TYPES);

        return AppEntityManagerFactory::create(
            $parameters,
            $mappings,
            $customTypes,
            $environment === 'dev'
        );
    }
}
