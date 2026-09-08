<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\ORMSetup;
use InvalidArgumentException;

class AppEntityManagerFactory
{
    public static function create(
        array $parameters,
        array $mappingPrefixes,
        array $customTypes,
        bool $isDevMode
    ): EntityManagerInterface {
        $config = self::createConfiguration($isDevMode, $mappingPrefixes);
        $connection = DriverManager::getConnection($parameters, $config);
        self::registerCustomTypes($customTypes);
        return new EntityManager($connection, $config);
    }

    private static function createConfiguration(bool $isDevMode, array $mappingPrefixes): Configuration
    {
        $config = ORMSetup::createConfiguration($isDevMode);
        $xmlDriver = new SimplifiedXmlDriver($mappingPrefixes);
        $config->setMetadataDriverImpl($xmlDriver);

        $config->enableNativeLazyObjects(true);
        return $config;
    }

    private static function registerCustomTypes(array $registeredCustomTypes): void
    {
        array_walk(
            $registeredCustomTypes,
            function (string $customType) {
                if (!is_subclass_of($customType, DoctrineCustomType::class)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'The custom type "%s" must implement interface %s',
                            $customType,
                            DoctrineCustomType::class
                        )
                    );
                }

                if (!Type::hasType($customType::customTypeName())) {
                    Type::addType($customType::customTypeName(), $customType);
                }
            }
        );
    }
}
