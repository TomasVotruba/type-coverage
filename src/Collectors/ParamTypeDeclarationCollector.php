<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Collectors;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @see \TomasVotruba\TypeCoverage\Rules\ParamTypeCoverageRule
 */
final class ParamTypeDeclarationCollector implements Collector
{
    /**
     * @readonly
     * @var \PhpParser\PrettyPrinter\Standard
     */
    private $printerStandard;

    public function __construct(Standard $printerStandard)
    {
        $this->printerStandard = $printerStandard;
    }

    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return array{int, int, string}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipFunctionLike($node)) {
            return [0, 0, ''];
        }

        $paramCount = count($node->getParams());

        $typedParamCount = 0;
        foreach ($node->getParams() as $param) {
            if ($param->variadic) {
                // skip variadic
                --$paramCount;
                continue;
            }

            if ($param->type === null) {
                continue;
            }

            ++$typedParamCount;
        }

        // missing at least 1 type
        $printedClassMethod = $paramCount !== $typedParamCount ? $this->printerStandard->prettyPrint([$node]) : '';

        return [$typedParamCount, $paramCount, $printedClassMethod];
    }

    private function shouldSkipFunctionLike(FunctionLike $functionLike): bool
    {
        // nothing to analyse
        if ($functionLike->getParams() === []) {
            return true;
        }

        return $this->hasFunctionLikeCallableParam($functionLike);
    }

    private function hasFunctionLikeCallableParam(FunctionLike $functionLike): bool
    {
        // skip callable, can be anythings
        $docComment = $functionLike->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $docCommentText = $docComment->getText();
        return strpos($docCommentText, '@param callable') !== false;
    }
}
