<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

final class AlphaPlainUntypedMethod
{
    public function untyped()
    {
        return 1;
    }
}
