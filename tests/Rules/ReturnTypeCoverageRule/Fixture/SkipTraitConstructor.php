<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Fixture;

final class SkipTraitConstructor
{
    use TraitWithConstructor {
		TraitWithConstructor::__construct as traitConstruct;
    }

    public function __construct()
    {
        $this->traitConstruct();
    }
}

trait TraitWithConstructor
{
    public function __construct()
    {
    }
}
