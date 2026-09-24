<?php

declare(strict_types=1);

namespace Rector\TypePerfect\Tests\Rules\NarrowPublicClassMethodParamTypeRule\Source\DoctrineMapping;

use Rector\TypePerfect\Tests\Rules\NarrowPublicClassMethodParamTypeRule\Fixture\SkipDoctrineDocument;
use Rector\TypePerfect\Tests\Rules\NarrowPublicClassMethodParamTypeRule\Fixture\SkipDoctrineEntity;
use Rector\TypePerfect\Tests\Rules\NarrowPublicClassMethodParamTypeRule\Fixture\SkipDoctrineEntityAnnotation;

final class CallDoctrineMapping
{
    public function run(
        SkipDoctrineEntity $skipDoctrineEntity,
        SkipDoctrineDocument $skipDoctrineDocument,
        SkipDoctrineEntityAnnotation $skipDoctrineEntityAnnotation
    ): void {
        $skipDoctrineEntity->setName('John');
        $skipDoctrineDocument->setName('John');
        $skipDoctrineEntityAnnotation->setName('John');
    }
}
