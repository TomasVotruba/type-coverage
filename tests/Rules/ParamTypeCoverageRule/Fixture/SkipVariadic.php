<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Fixture;

final class SkipVariadic
{
    public function run(...$items)
    {
    }
}
