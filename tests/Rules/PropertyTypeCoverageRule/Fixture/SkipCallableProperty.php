<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\PropertyTypeCoverageRule\Fixture;

final class SkipCallableProperty
{
    /**
     * @var callable
     */
    public $someCallable;
}
