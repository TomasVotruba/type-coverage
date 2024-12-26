<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use TomasVotruba\TypeCoverage\CollectorDataNormalizer;
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
    public const ERROR_MESSAGE = 'Out of %d possible param types, only %d - %.1f %% actually have it. Add more param types to get over %s %%';

    /**
     * @var string
     */
    private const IDENTIFIER = 'typeCoverage.paramTypeCoverage';

    /**
     * @readonly
     */
    private TypeCoverageFormatter $typeCoverageFormatter;

    /**
     * @readonly
     */
    private Configuration $configuration;

    /**
     * @readonly
     */
    private CollectorDataNormalizer $collectorDataNormalizer;

    public function __construct(TypeCoverageFormatter $typeCoverageFormatter, Configuration $configuration, CollectorDataNormalizer $collectorDataNormalizer)
    {
        $this->typeCoverageFormatter = $typeCoverageFormatter;
        $this->configuration = $configuration;
        $this->collectorDataNormalizer = $collectorDataNormalizer;
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
        $paramTypeDeclarationCollector = $node->get(ParamTypeDeclarationCollector::class);

        $typeCountAndMissingTypes = $this->collectorDataNormalizer->normalize($paramTypeDeclarationCollector);

        if ($this->configuration->showOnlyMeasure()) {
            return [
                sprintf(
                    'Param type coverage is %.1f %% out of %d possible',
                    $typeCountAndMissingTypes->getCoveragePercentage(),
                    $typeCountAndMissingTypes->getTotalCount()
                ),
            ];
        }

        if ($this->configuration->getRequiredParamTypeLevel() === 0) {
            return [];
        }

        return $this->typeCoverageFormatter->formatErrors(
            self::ERROR_MESSAGE,
            self::IDENTIFIER,
            $this->configuration->getRequiredParamTypeLevel(),
            $typeCountAndMissingTypes
        );
    }
}
