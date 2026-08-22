<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source;

trait AlphaSameLineTrait
{
    public function alphaUntyped()
    {
        return 1;
    }
}
