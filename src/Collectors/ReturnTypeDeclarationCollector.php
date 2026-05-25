<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Collectors;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ClassReflection;

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

        // skip methods inherited from a parent class or interface, as types are locked by LSP
        if ($this->isGuardedByParentMethod($scope, $node)) {
            return null;
        }

        $missingTypeLines = [];

        if (! $node->returnType instanceof Node) {
            $missingTypeLines[] = $node->getLine();
        }

        return [1, $missingTypeLines];
    }

    private function isGuardedByParentMethod(Scope $scope, ClassMethod $classMethod): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $methodName = $classMethod->name->toString();

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if ($parentClassReflection->hasMethod($methodName)) {
                return true;
            }
        }

        foreach ($classReflection->getInterfaces() as $interfaceReflection) {
            if ($interfaceReflection->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
