<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use TomasVotruba\TypeCoverage\Collectors\FunctionLike\ParamTypeSeaLevelCollector;
use TomasVotruba\TypeCoverage\Collectors\ParamTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Formatter\TypeCoverageFormatter;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\ParamTypeCoverageRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class ParamTypeCoverageRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible param types, only %d %% actually have it. Add more param types to get over %d %%';

    public function __construct(
        private TypeCoverageFormatter $seaLevelRuleErrorFormatter,
        private float $minimalLevel = 0.80,
        private bool $printSuggestions = true
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $paramSeaLevelDataByFilePath = $node->get(ParamTypeDeclarationCollector::class);

        $typedParamCount = 0;
        $paramCount = 0;

        $printedClassMethods = [];

        foreach ($paramSeaLevelDataByFilePath as $paramSeaLevelData) {
            foreach ($paramSeaLevelData as $nestedParamSeaLevelData) {
                $typedParamCount += $nestedParamSeaLevelData[0];
                $paramCount += $nestedParamSeaLevelData[1];

                if (! $this->printSuggestions) {
                    continue;
                }

                /** @var string $printedClassMethod */
                $printedClassMethod = $nestedParamSeaLevelData[2];
                if ($printedClassMethod !== '') {
                    $printedClassMethods[] = trim($printedClassMethod);
                }
            }
        }

        return $this->seaLevelRuleErrorFormatter->formatErrors(
            self::ERROR_MESSAGE,
            $this->minimalLevel,
            $paramCount,
            $typedParamCount,
            $printedClassMethods
        );
    }
}
