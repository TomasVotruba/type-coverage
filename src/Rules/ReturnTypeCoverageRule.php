<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use TomasVotruba\TypeCoverage\CollectorDataNormalizer;
use TomasVotruba\TypeCoverage\Collectors\ReturnTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Configuration;
use TomasVotruba\TypeCoverage\Formatter\TypeCoverageFormatter;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\ReturnTypeCoverageRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final readonly class ReturnTypeCoverageRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible return types, only %d - %.1f %% actually have it. Add more return types to get over %s %%';

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
        $returnSeaLevelDataByFilePath = $node->get(ReturnTypeDeclarationCollector::class);
        $typeCountAndMissingTypes = $this->collectorDataNormalizer->normalize($returnSeaLevelDataByFilePath);

        if ($this->configuration->showOnlyMeasure()) {
            return [
                sprintf(
                    'Return type coverage is %.1f %% out of %d possible',
                    $typeCountAndMissingTypes->getCoveragePercentage(),
                    $typeCountAndMissingTypes->getTotalCount()
                ),
            ];
        }

        if ($this->configuration->getRequiredReturnTypeLevel() === 0) {
            return [];
        }

        return $this->typeCoverageFormatter->formatErrors(
            self::ERROR_MESSAGE,
            $this->configuration->getRequiredReturnTypeLevel(),
            $typeCountAndMissingTypes
        );
    }
}
