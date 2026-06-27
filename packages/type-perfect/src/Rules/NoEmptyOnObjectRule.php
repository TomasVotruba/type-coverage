<?php

declare(strict_types=1);

namespace Rector\TypePerfect\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Empty_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Rector\TypePerfect\Guard\EmptyIssetGuard;

/**
 * @implements Rule<Empty_>
 */
final readonly class NoEmptyOnObjectRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use instanceof instead of empty() on object';

    public function __construct(
        private EmptyIssetGuard $emptyIssetGuard
    ) {
    }

    public function getNodeType(): string
    {
        return Empty_::class;
    }

    /**
     * @param Empty_ $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->emptyIssetGuard->isLegal($node->expr, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->identifier('typePerfect.noEmptyOnObject')
                ->build(),
        ];
    }
}
