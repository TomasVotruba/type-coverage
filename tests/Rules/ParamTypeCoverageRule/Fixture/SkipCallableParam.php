<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\ParamTypeCoverageRule\Fixture;

final class SkipCallableParam
{
    /**
     * @param callable $callable
     */
    public function run($callable)
    {
    }
}
