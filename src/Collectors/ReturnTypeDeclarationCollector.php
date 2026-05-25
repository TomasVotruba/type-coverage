<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Collectors;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\MixedType;

/**
 * @see \TomasVotruba\TypeCoverage\Rules\ReturnTypeCoverageRule
 */
final class ReturnTypeDeclarationCollector implements Collector
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return mixed[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        // skip magic
        if ($node->isMagic()) {
            return null;
        }

        if ($scope->isInTrait()) {
            $originalMethodName = $node->getAttribute('originalTraitMethodName');
            if ($originalMethodName === '__construct') {
                return null;
            }
        }

        $missingTypeLines = [];

        if (! $node->returnType instanceof Node && ! $this->isReturnGuardedByParentMethod($scope, $node)) {
            $missingTypeLines[] = $node->getLine();
        }

        return [1, $missingTypeLines];
    }

    private function isReturnGuardedByParentMethod(Scope $scope, ClassMethod $classMethod): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $methodName = $classMethod->name->toString();

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if (! $parentClassReflection->hasNativeMethod($methodName)) {
                continue;
            }

            $variants = $parentClassReflection->getNativeMethod($methodName)
                ->getVariants();
            if ($variants === []) {
                continue;
            }

            $nativeReturnType = $variants[0]->getNativeReturnType();
            if ($nativeReturnType instanceof MixedType && ! $nativeReturnType->isExplicitMixed()) {
                return true;
            }
        }

        return false;
    }
}
