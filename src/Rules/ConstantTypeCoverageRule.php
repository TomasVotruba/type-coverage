<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use TomasVotruba\TypeCoverage\CollectorDataNormalizer;
use TomasVotruba\TypeCoverage\Collectors\ConstantTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Configuration;
use TomasVotruba\TypeCoverage\Formatter\TypeCoverageFormatter;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\ConstantTypeCoverageRule\ConstantTypeCoverageRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final readonly class ConstantTypeCoverageRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible constant types, only %d - %.1f %% actually have it. Add more constant types to get over %s %%';

    /**
     * @var string
     */
    private const IDENTIFIER = 'typeCoverage.constantTypeCoverage';

    public function __construct(
        private TypeCoverageFormatter $typeCoverageFormatter,
        private Configuration $configuration,
        private CollectorDataNormalizer $collectorDataNormalizer,
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
        $constantTypeDeclarationCollector = $node->get(ConstantTypeDeclarationCollector::class);
        $typeCountAndMissingTypes = $this->collectorDataNormalizer->normalize($constantTypeDeclarationCollector);

        if ($this->configuration->showOnlyMeasure()) {
            return [
                sprintf(
                    'Class constant type coverage is %.1f %% out of %d possible',
                    $typeCountAndMissingTypes->getCoveragePercentage(),
                    $typeCountAndMissingTypes->getTotalCount()
                ),
            ];
        }

        if (! $this->configuration->isConstantTypeCoverageEnabled()) {
            return [];
        }

        return $this->typeCoverageFormatter->formatErrors(
            self::ERROR_MESSAGE,
            self::IDENTIFIER,
            $this->configuration->getRequiredConstantTypeLevel(),
            $typeCountAndMissingTypes
        );
    }
}
