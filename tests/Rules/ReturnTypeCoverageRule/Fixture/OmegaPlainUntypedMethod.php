<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

final class OmegaPlainUntypedMethod
{
    public function untyped()
    {
        return 1;
    }
}
