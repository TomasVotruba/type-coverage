<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Fixture;

final class SkipParentMethodOverride extends SkipParentMethodOverrideParent
{
    /**
     * @param string $name
     */
    public function run($name)
    {
    }
}
