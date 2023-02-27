<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Collectors;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;

/**
 * @see \TomasVotruba\TypeCoverage\Rules\PropertyTypeCoverageRule
 */
final class PropertyTypeDeclarationCollector implements Collector
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

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return array{int, int, string}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $printedProperties = '';

        // return typed properties/all properties
        $classLike = $node->getOriginalNode();

        $propertyCount = count($classLike->getProperties());

        $typedPropertyCount = 0;

        foreach ($classLike->getProperties() as $property) {
            // blocked by parent type
            if ($this->isGuardedByParentClassProperty($scope, $property)) {
                ++$typedPropertyCount;
                continue;
            }

            // already typed
            if ($property->type instanceof Node) {
                ++$typedPropertyCount;
                continue;
            }

            if ($this->isPropertyDocTyped($property)) {
                ++$typedPropertyCount;
                continue;
            }

            // give useful context
            $printedProperties .= PHP_EOL . PHP_EOL . $this->printerStandard->prettyPrint([$property]);
        }

        return [$typedPropertyCount, $propertyCount, $printedProperties];
    }

    private function isPropertyDocTyped(Property $property): bool
    {
        $docComment = $property->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $docCommentText = $docComment->getText();

        // skip as unable to type
        return strpos($docCommentText, 'callable') !== false || strpos($docCommentText, 'resource') !== false;
    }

    private function isGuardedByParentClassProperty(Scope $scope, Property $property): bool
    {
        $propertyName = $property->props[0]->name->toString();

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classReflection->getParents() as $parentClassReflection) {
            if ($parentClassReflection->hasProperty($propertyName)) {
                return true;
            }
        }

        return false;
    }
}
