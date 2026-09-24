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
    private const array SKIPPED_TYPES = [
        'PHPUnit\Framework\TestCase',
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
    ];

    /**
     * @var string[]
     */
    private const array DOCTRINE_MAPPING_ATTRIBUTES = [
        'Doctrine\ORM\Mapping\Entity',
        'Doctrine\ODM\MongoDB\Mapping\Annotations\Document',
    ];

    /**
     * @var string[]
     */
    private const array DOCTRINE_MAPPING_ANNOTATIONS = ['@ORM\Entity', '@ODM\Document', '@MongoDB\Document'];

    public function shouldSkipClassReflection(ClassReflection $classReflection): bool
    {
        // skip interface as required, traits as unable to detect for sure
        if (! $classReflection->isClass()) {
            return true;
        }

        // skip Doctrine entities and documents, their setters are called by hydration too
        if ($this->isDoctrineEntityOrDocument($classReflection)) {
            return true;
        }

        return array_any(self::SKIPPED_TYPES, fn (string $skippedType): bool => $classReflection->is($skippedType));
    }

    public function isUsedByParentClassOrInterface(ClassReflection $classReflection, string $methodName): bool
    {
        // is this method required by parent contract? skip it
        foreach ($classReflection->getInterfaces() as $parentInterfaceReflection) {
            if ($parentInterfaceReflection->hasMethod($methodName)) {
                return true;
            }
        }

        return array_any($classReflection->getParents(), fn (ClassReflection $parentClassReflection): bool => $parentClassReflection->hasMethod($methodName));
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

    private function isDoctrineEntityOrDocument(ClassReflection $classReflection): bool
    {
        $nativeReflection = $classReflection->getNativeReflection();

        foreach ($nativeReflection->getAttributes() as $reflectionAttribute) {
            if (in_array($reflectionAttribute->getName(), self::DOCTRINE_MAPPING_ATTRIBUTES, true)) {
                return true;
            }
        }

        $docComment = $nativeReflection->getDocComment();
        if (! is_string($docComment)) {
            return false;
        }

        return array_any(
            self::DOCTRINE_MAPPING_ANNOTATIONS,
            fn (string $annotation): bool => str_contains($docComment, $annotation)
        );
    }
}
