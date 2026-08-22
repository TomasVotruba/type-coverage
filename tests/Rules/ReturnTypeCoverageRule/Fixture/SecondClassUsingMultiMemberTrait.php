<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

use TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source\MultiMemberTrait;

final class SecondClassUsingMultiMemberTrait
{
    use MultiMemberTrait;
}
