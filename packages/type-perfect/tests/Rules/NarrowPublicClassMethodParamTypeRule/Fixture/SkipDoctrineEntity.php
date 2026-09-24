<?php

declare(strict_types=1);

namespace Rector\TypePerfect\Tests\Rules\NarrowPublicClassMethodParamTypeRule\Fixture;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class SkipDoctrineEntity
{
    public function setName(int|string $name): void
    {
    }
}
