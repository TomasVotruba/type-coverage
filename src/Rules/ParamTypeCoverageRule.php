<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use TomasVotruba\TypeCoverage\Collectors\ParamTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Configuration;
use TomasVotruba\TypeCoverage\Formatter\TypeCoverageFormatter;

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

    /**
     * @readonly
     * @var \TomasVotruba\TypeCoverage\Formatter\TypeCoverageFormatter
     */
    private $typeCoverageFormatter;

    /**
     * @readonly
     * @var \TomasVotruba\TypeCoverage\Configuration
     */
    private $configuration;

    public function __construct(TypeCoverageFormatter $typeCoverageFormatter, Configuration $configuration)
    {
        $this->typeCoverageFormatter = $typeCoverageFormatter;
        $this->configuration = $configuration;
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
        if ($this->configuration->getRequiredParamTypeLevel() === 0) {
            return [];
        }

        $paramTypeDeclarationCollector = $node->get(ParamTypeDeclarationCollector::class);

        $typedParamCount = 0;
        $paramCount = 0;

        $printedClassMethods = [];

        foreach ($paramTypeDeclarationCollector as $paramSeaLevelData) {
            foreach ($paramSeaLevelData as $nestedParamSeaLevelData) {
                $typedParamCount += $nestedParamSeaLevelData[0];
                $paramCount += $nestedParamSeaLevelData[1];

                if (! $this->configuration->shouldPrintSuggestions()) {
                    continue;
                }

                /** @var string $printedClassMethod */
                $printedClassMethod = $nestedParamSeaLevelData[2];
                if ($printedClassMethod !== '') {
                    $printedClassMethods[] = trim($printedClassMethod);
                }
            }
        }

        return $this->typeCoverageFormatter->formatErrors(
            self::ERROR_MESSAGE,
            $this->configuration->getRequiredParamTypeLevel(),
            $paramCount,
            $typedParamCount,
            $printedClassMethods
        );
    }
}
