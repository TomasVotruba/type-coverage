<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Fixture;

final class UnknownParamType
{
    public function run(string $name, $age)
    {
    }

    public function again($city)
    {
    }
}
