<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withSkip([
        // these fixtures use PHP 8.3 features, we cannot check them wit lower versions
        __DIR__ . '/tests/Rules/ConstantTypeCoverageRule/Fixture/SkipKnownConstantType.php',
        __DIR__ . '/tests/Rules/ConstantTypeCoverageRule/Fixture/UnknownConstantType.php',
    ])
    ->withPreparedSets(common: true, psr12: true, cleanCode: true, symplify: true);
