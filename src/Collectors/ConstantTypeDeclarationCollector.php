<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Collectors;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Node\ClassConstantsNode;
use PHPStan\Reflection\ClassReflection;

/**
 * @see \TomasVotruba\TypeCoverage\Rules\ConstantTypeCoverageRule
 */
final class ConstantTypeDeclarationCollector implements Collector
{
    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassConstantsNode::class;
    }

    /**
     * @param ClassConstantsNode $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // return typed properties/all properties
        if (! $node instanceof ClassConstantsNode) {
            throw new \LogicException('Node is ' . $node::class . ' instead of ' . ClassConstantsNode::class);
        }

        $constantCount = count($node->getConstants());

        $missingTypeLines = [];

        foreach ($node->getConstants() as $constant) {
            // blocked by parent type
            if ($this->isGuardedByParentClassConstant($scope, $constant)) {
                continue;
            }

            // already typed
            if ($constant->type instanceof Node) {
                continue;
            }

            // give useful context
            $missingTypeLines[] = $constant->getLine();
        }

        return [$constantCount, $missingTypeLines];
    }

    private function isGuardedByParentClassConstant(Scope $scope, ClassConst $const): bool
    {
        $constName = $const->consts[0]->name->toString();

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if ($parentClassReflection->hasConstant($constName)) {
                return true;
            }
        }

        return false;
    }
}
