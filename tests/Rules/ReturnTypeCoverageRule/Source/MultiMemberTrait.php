<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\Source;

trait MultiMemberTrait
{
    public function firstUntyped()
    {
        return 1;
    }

    public function secondUntyped()
    {
        return 2;
    }

    public function typed(): int
    {
        return 3;
    }
}
