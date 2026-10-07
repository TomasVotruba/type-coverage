<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Collectors;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

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
     * @return array{count: int, missingLines: list<int>, traitFilePath: string|null, startFilePos: int}|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        // skip magic
        if ($node->isMagic()) {
            return null;
        }

        $traitFilePath = null;
        if ($scope->isInTrait()) {
            $originalMethodName = $node->getAttribute('originalTraitMethodName');
            if ($originalMethodName === '__construct') {
                return null;
            }

            $traitFilePath = $scope->getTraitReflection()
                ->getFileName();
        }

        $missingTypeLines = [];

        if (! $node->returnType instanceof Node) {
            $missingTypeLines[] = $node->getLine();
        }

        return [
            'count' => 1,
            'missingLines' => $missingTypeLines,
            'traitFilePath' => $traitFilePath,
            'startFilePos' => $node->getStartFilePos(),
        ];
    }
}
