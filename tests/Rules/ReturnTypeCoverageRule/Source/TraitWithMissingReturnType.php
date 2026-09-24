<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source;

trait TraitWithMissingReturnType
{
    public function traitMethod()
    {
        return 1;
    }
}
