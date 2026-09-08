<?php

require_once __DIR__ . '/tools/php-cs-fixer/NoTrailingCommaInMultilineFixer.php';

use App\Tools\PhpCsFixer\NoTrailingCommaInMultilineFixer;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('tools')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ])
;

return (new PhpCsFixer\Config())
    ->registerCustomFixers([new NoTrailingCommaInMultilineFixer()])
    ->setRules([
        '@PSR12' => true,

        'trailing_comma_in_multiline' => false,
        'no_trailing_comma_in_singleline' => true,
        'App/no_trailing_comma_in_multiline' => true,

        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],

        'multiline_promoted_properties' => [
            'minimum_number_of_parameters' => 1,
        ],

        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
    ])
    ->setFinder($finder)
;
