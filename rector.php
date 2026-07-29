<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ConstFetch\RemovePhpVersionIdCheckRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/packages/type-perfect/src',
        __DIR__ . '/packages/type-perfect/tests',
    ])
    ->withRootFiles()
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true
    )
    ->withImportNames(removeUnusedImports: true)
    ->withSkip([
        '*/Fixture/*',
        '*/Source/*',
        RemovePhpVersionIdCheckRector::class => [
            // this package is downgraded, so PHP version checks are expected
            __DIR__ . '/src',
        ],
        StringClassNameToClassConstantRector::class => [
            // allow-lists name classes from optional dev-only packages, they must stay strings
            __DIR__ . '/packages/type-perfect/src',
        ],
    ]);
