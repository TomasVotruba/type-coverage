<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\ParamTypeCoverageRule\Fixture;

final class SkipVariadic
{
    public function run(... $items)
    {
    }
}
