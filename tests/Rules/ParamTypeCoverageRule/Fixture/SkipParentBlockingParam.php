<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Fixture;

use TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Source\ParentWithDocBlockOnlyParam;

final class SkipParentBlockingParam extends ParentWithDocBlockOnlyParam
{
    public function run($name)
    {
    }
}
