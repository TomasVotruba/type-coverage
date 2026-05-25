<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

final class SkipParentMethodOverride extends SkipParentMethodOverrideParent
{
    /**
     * @return int
     */
    public function run()
    {
        return 2;
    }
}
