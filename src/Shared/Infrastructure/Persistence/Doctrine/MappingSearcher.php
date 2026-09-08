<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine;

final class MappingSearcher
{
    private const string MAPPINGS_PATH = '/Infrastructure/Persistence/Doctrine';

    public static function inContext(?string $context, array $contextsToExclude = [], array $modulesToExclude = []): array
    {
        $contextBasePath = self::retrieveContextPath($context);
        $mappings = [];
        $contexts = self::retrieveContexts($contextBasePath);

        foreach ($contexts as $singleContext) {
            $modules = self::retrieveModules("$contextBasePath/$singleContext");
            if (!in_array($singleContext, $contextsToExclude)) {
                if ($singleContext === 'Shared') {
                    $namespace = "App\\$singleContext\\Domain";
                    $mappings[$contextBasePath . $singleContext . self::MAPPINGS_PATH] = $namespace;
                } else {
                    foreach ($modules as $module) {
                        if ($module === 'Shared' || in_array($module, $modulesToExclude)) {
                            continue;
                        }
                        $mappingPath = $contextBasePath . $singleContext . '/' . $module . self::MAPPINGS_PATH;

                        if (!is_dir($mappingPath)) {
                            continue;
                        }

                        $namespace = $singleContext ? "App\\$singleContext\\$module\\Domain" : "App\\$module\\Domain";
                        $mappings[$mappingPath] = $namespace;
                    }
                }
            }
        }

        return $mappings;
    }

    private static function retrieveContextPath(?string $context): string
    {
        $rootPath = str_replace('public', '', $_SERVER['DOCUMENT_ROOT']);
        return sprintf('%s%s/%s', $rootPath, 'src', $context ?? '');
    }

    private static function retrieveContexts(string $contextBasePath): array
    {
        $modules = scandir($contextBasePath);
        return array_filter($modules, fn ($module) => (!in_array($module, ['..', '.', 'Kernel.php'])));
    }

    private static function retrieveModules(string $contextBasePath): array
    {
        $modules = scandir($contextBasePath);
        return array_filter($modules, fn ($module) => (!in_array($module, ['..', '.', 'Kernel.php'])));
    }
}
