<?php

declare(strict_types=1);

use Rector\DeadCode\Rector\ConstFetch\RemovePhpVersionIdCheckRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
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
    ]);
