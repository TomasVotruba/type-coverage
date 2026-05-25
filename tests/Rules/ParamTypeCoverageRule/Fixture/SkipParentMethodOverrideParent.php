<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Fixture;

abstract class SkipParentMethodOverrideParent
{
    /**
     * @param string $name
     */
    public function run($name)
    {
    }
}
