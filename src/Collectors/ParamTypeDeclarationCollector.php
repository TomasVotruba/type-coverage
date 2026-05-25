<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Collectors;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ClassReflection;

/**
 * @see \TomasVotruba\TypeCoverage\Rules\ParamTypeCoverageRule
 */
final class ParamTypeDeclarationCollector implements Collector
{
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return mixed[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if ($this->shouldSkipFunctionLike($node)) {
            return null;
        }

        $missingTypeLines = [];
        $paramCount = count($node->getParams());

        foreach ($node->getParams() as $position => $param) {
            if ($param->variadic) {
                // skip variadic
                --$paramCount;
                continue;
            }

            if ($param->type !== null) {
                continue;
            }

            // parent method's param has no native type → LSP prevents adding one in child
            if ($this->isParamGuardedByParentMethod($scope, $node, $position)) {
                continue;
            }

            $missingTypeLines[] = $param->getLine();
        }

        return [$paramCount, $missingTypeLines];
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
        return str_contains($docCommentText, '@param callable');
    }

    private function isParamGuardedByParentMethod(Scope $scope, FunctionLike $functionLike, int $position): bool
    {
        if (! $functionLike instanceof ClassMethod) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $methodName = $functionLike->name->toString();

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if (! $parentClassReflection->hasNativeMethod($methodName)) {
                continue;
            }

            $variants = $parentClassReflection->getNativeMethod($methodName)
                ->getVariants();
            if ($variants === []) {
                continue;
            }

            $parentParameters = $variants[0]->getParameters();
            if (! isset($parentParameters[$position])) {
                continue;
            }

            if (! $parentParameters[$position]->hasNativeType()) {
                return true;
            }
        }

        return false;
    }
}
