<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

use TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source\TraitWithMissingReturnType;

final class SecondClassUsingTrait
{
    use TraitWithMissingReturnType;
}
