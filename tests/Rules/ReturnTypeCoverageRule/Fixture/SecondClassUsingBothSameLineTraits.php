<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

use TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source\AlphaSameLineTrait;
use TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source\OmegaSameLineTrait;

final class SecondClassUsingBothSameLineTraits
{
    use AlphaSameLineTrait;
    use OmegaSameLineTrait;
}
