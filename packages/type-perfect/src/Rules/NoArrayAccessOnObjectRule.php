<?php

declare(strict_types=1);

namespace Rector\TypePerfect\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;

/**
 * @see \Rector\TypePerfect\Tests\Rules\NoArrayAccessOnObjectRule\NoArrayAccessOnObjectRuleTest
 * @implements Rule<ArrayDimFetch>
 */
final class NoArrayAccessOnObjectRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit methods over array access on object';

    /**
     * @var string[]
     */
    private const ALLOWED_CLASSES = [
        'SplFixedArray',
        'SimpleXMLElement',
        'Iterator',
        'WeakMap',
        'Aws\ResultInterface',
        'Symfony\Component\Form\FormInterface',
        'Symfony\Component\OptionsResolver\Options',
    ];

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ArrayDimFetch::class;
    }

    /**
     * @param ArrayDimFetch $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $varType = $scope->getType($node->var);
        if (! $varType instanceof ObjectType) {
            return [];
        }

        if ($this->isAllowedObjectType($varType)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier('typePerfect.noArrayAccessOnObject')
                ->build(),
        ];
    }

    private function isAllowedObjectType(ObjectType $objectType): bool
    {
        foreach (self::ALLOWED_CLASSES as $allowedClass) {
            if ($objectType->isInstanceOf($allowedClass)->yes()) {
                return true;
            }
        }

        return false;
    }
}
