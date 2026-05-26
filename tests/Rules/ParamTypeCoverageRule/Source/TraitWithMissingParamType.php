<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Source;

trait TraitWithMissingParamType
{
    public function traitMethod($value): void
    {
    }
}
