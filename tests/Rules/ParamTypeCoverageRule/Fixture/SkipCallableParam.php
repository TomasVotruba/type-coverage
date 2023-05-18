<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Fixture;

final class SkipCallableParam
{
    /**
     * @param callable $callable
     */
    public function run($callable)
    {
    }
}
