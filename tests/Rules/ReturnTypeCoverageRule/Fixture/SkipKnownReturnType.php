<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\ReturnTypeDeclarationSeaLevelRule\Fixture;

final class SkipKnownReturnType
{
    public function run(): int
    {
        return 1000;
    }
}
