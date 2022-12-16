<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Collectors;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<ClassMethod, array{int, int, string}>>
 *
 * @see \TomasVotruba\TypeCoverage\Rules\ReturnTypeCoverageRule
 */
final class ReturnTypeDeclarationCollector implements Collector
{
    public function __construct(
        private readonly Standard $printerStandard
    ) {
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return array{int, int, string}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // skip magic
        if ($node->isMagic()) {
            return [0, 0, ''];
        }

        if ($node->returnType instanceof Node) {
            $typedReturnCount = 1;
            $printedNode = '';
        } else {
            $typedReturnCount = 0;
            $printedNode = $this->printerStandard->prettyPrint([$node]);
        }

        return [$typedReturnCount, 1, $printedNode];
    }
}
