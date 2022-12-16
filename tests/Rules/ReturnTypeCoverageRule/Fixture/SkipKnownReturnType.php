<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

final class SkipKnownReturnType
{
    public function run(): int
    {
        return 1000;
    }
}
