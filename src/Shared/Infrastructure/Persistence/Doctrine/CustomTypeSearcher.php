<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine;

final class CustomTypeSearcher
{
    public static function fromPaths(array $paths): array
    {
        $customTypes = [];
        foreach ($paths as $path) {
            self::searchTheEntireDirectory($customTypes, $path, self::buildNamespaceFromPath($path));
        }
        return $customTypes;
    }

    private static function searchTheEntireDirectory(array &$customTypes, string $path, string $namespace): void
    {
        $resources = scandir($path);
        $resources = array_filter($resources, fn ($resource) => (!in_array($resource, ['..', '.'])));
        foreach ($resources as $resource) {
            if ($namespace === 'App\\Shared\\Infrastructure\\Persistence\\Doctrine\\Types') {
                continue;
            }
            if (preg_match('/Type.php$/', $resource)) {
                $class = str_replace('.php', '', $resource);
                $customTypes[] = $namespace . '\\' . $class;
            }
            if (is_dir("$path/$resource")) {
                self::searchTheEntireDirectory(
                    $customTypes,
                    "$path/$resource",
                    $namespace . '\\' .$resource
                );
            }
        }
    }

    private static function buildNamespaceFromPath(string $path): string
    {
        $contextBasePath = explode('src', $path)[1];
        return 'App' . str_replace('/', '\\', $contextBasePath);
    }
}
