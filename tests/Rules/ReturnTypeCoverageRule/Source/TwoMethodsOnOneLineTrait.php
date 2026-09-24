<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source;

trait TwoMethodsOnOneLineTrait
{
    public function first() { return 1; } public function second() { return 2; }
}
