<?php

declare(strict_types=1);

namespace Rector\TypePerfect\Tests\Rules\NoMixedMethodCallerRule\Fixture;

final class SkipArrayDimFetchCaller
{
    public function run(array $config)
    {
        $config['permissionObject']->run();
    }
}
