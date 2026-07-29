<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/packages/type-perfect/src',
        __DIR__ . '/packages/type-perfect/tests',
    ])
    ->withRootFiles()
    ->withSkip([
        // these fixtures use PHP 8.3 features, we cannot check them wit lower versions
        __DIR__ . '/tests/Rules/ConstantTypeCoverageRule/Fixture/SkipKnownConstantType.php',
        __DIR__ . '/tests/Rules/ConstantTypeCoverageRule/Fixture/UnknownConstantType.php',

        // fixtures assert exact error lines, formatting them would shift the lines
        __DIR__ . '/packages/type-perfect/tests/*/Fixture/*',
        __DIR__ . '/packages/type-perfect/tests/*/Source/*',
    ])
    ->withPreparedSets(common: true, psr12: true, cleanCode: true, symplify: true);
