<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source;

class ParentWithDocBlockOnlyReturn
{
    /**
     * @return string
     */
    public function run()
    {
        return 'value';
    }
}
