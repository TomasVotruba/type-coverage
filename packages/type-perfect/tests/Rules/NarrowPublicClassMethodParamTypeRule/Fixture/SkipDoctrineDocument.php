<?php

declare(strict_types=1);

namespace Rector\TypePerfect\Tests\Rules\NarrowPublicClassMethodParamTypeRule\Fixture;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
final class SkipDoctrineDocument
{
    public function setName(int|string $name): void
    {
    }
}
