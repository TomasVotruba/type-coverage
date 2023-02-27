<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\PropertyTypeCoverageRule\Fixture;

use TomasVotruba\TypeCoverage\Tests\Rules\PropertyTypeCoverageRule\Source\ParentBlockingClass;

final class SkipParentBlockingProperty extends ParentBlockingClass
{
    /**
     * @var string[]
     */
    public $noTypes = ['first_no_type', 'second_no_type'];
}
