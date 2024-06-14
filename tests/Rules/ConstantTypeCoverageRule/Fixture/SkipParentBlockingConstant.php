<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ConstantTypeCoverageRule\Fixture;

use TomasVotruba\TypeCoverage\Tests\Rules\ConstantTypeCoverageRule\Source\ParentBlockingClass;

final class SkipParentBlockingConstant extends ParentBlockingClass
{
    /**
     * @var string[]
     */
    public const NO_TYPE = ['first_no_type', 'second_no_type'];
}
