<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

abstract class SkipParentMethodOverrideParent
{
    /**
     * @return int
     */
    public function run()
    {
        return 1;
    }
}
