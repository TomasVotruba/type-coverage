<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Source;

class ParentWithDocBlockOnlyParam
{
    /**
     * @param string $name
     */
    public function run($name)
    {
    }
}
