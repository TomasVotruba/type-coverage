<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use TomasVotruba\TypeCoverage\CollectorDataNormalizer;
use TomasVotruba\TypeCoverage\Configuration;
use TomasVotruba\TypeCoverage\Configuration\ScopeConfigurationResolver;
use TomasVotruba\TypeCoverage\Formatter\TypeCoverageFormatter;

/**
 * @implements Rule<CollectedDataNode>
 */
abstract class AbstractTypeCoverageRule implements Rule
{
    /**
     * @readonly
     */
    protected TypeCoverageFormatter $typeCoverageFormatter;

    /**
     * @readonly
     */
    protected Configuration $configuration;

    /**
     * @readonly
     */
    protected CollectorDataNormalizer $collectorDataNormalizer;

    public function __construct(
        TypeCoverageFormatter $typeCoverageFormatter,
        Configuration $configuration,
        CollectorDataNormalizer $collectorDataNormalizer
    ) {
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
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip()) {
            return [];
        }

        // if only subpaths are analysed, skip as data will be false positive
        if (! ScopeConfigurationResolver::areFullPathsAnalysed($scope)) {
            return [];
        }

        $collectorData = $node->get($this->getCollectorClass());
        $typeCountAndMissingTypes = $this->collectorDataNormalizer->normalize($collectorData);

        if ($this->configuration->showOnlyMeasure()) {
            $measureMessage = sprintf(
                $this->getMeasureMessage(),
                $typeCountAndMissingTypes->getCoveragePercentage(),
                $typeCountAndMissingTypes->getTotalCount()
            );

            return [RuleErrorBuilder::message($measureMessage)->build()];
        }

        if (! $this->isEnabled()) {
            return [];
        }

        return $this->typeCoverageFormatter->formatErrors(
            $this->getErrorMessage(),
            $this->getIdentifier(),
            $this->getRequiredTypeLevel(),
            $typeCountAndMissingTypes
        );
    }

    /**
     * @return class-string<Collector>
     */
    abstract protected function getCollectorClass(): string;

    abstract protected function getErrorMessage(): string;

    abstract protected function getIdentifier(): string;

    abstract protected function getMeasureMessage(): string;

    abstract protected function getRequiredTypeLevel(): float;

    protected function shouldSkip(): bool
    {
        return false;
    }

    protected function isEnabled(): bool
    {
        return $this->getRequiredTypeLevel() > 0;
    }
}
