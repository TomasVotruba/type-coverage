<?php

declare(strict_types=1);

namespace Rector\TypePerfect\Matcher\Collector;

use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;

final class PublicClassMethodMatcher
{
    /**
     * @var string[]
     */
    private const SKIPPED_TYPES = [
        'PHPUnit\Framework\TestCase',
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
    ];

    public function shouldSkipClassReflection(ClassReflection $classReflection): bool
    {
        // skip interface as required, traits as unable to detect for sure
        if (! $classReflection->isClass()) {
            return true;
        }

        foreach (self::SKIPPED_TYPES as $skippedType) {
            if ($classReflection->is($skippedType)) {
                return true;
            }
        }

        return false;
    }

    public function isUsedByParentClassOrInterface(ClassReflection $classReflection, string $methodName): bool
    {
        // is this method required by parent contract? skip it
        foreach ($classReflection->getInterfaces() as $parentInterfaceReflection) {
            if ($parentInterfaceReflection->hasMethod($methodName)) {
                return true;
            }
        }

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if ($parentClassReflection->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }

    public function shouldSkipClassMethod(ClassMethod $classMethod): bool
    {
        if ($classMethod->isMagic()) {
            return true;
        }

        if ($classMethod->isStatic()) {
            return true;
        }

        // skip attributes
        if ($classMethod->attrGroups !== []) {
            return true;
        }

        if (! $classMethod->isPublic()) {
            return true;
        }

        $doc = $classMethod->getDocComment();

        // skip symfony action
        return $doc instanceof Doc && str_contains($doc->getText(), '@Route');
    }
}
