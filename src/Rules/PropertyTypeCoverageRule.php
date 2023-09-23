<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use TomasVotruba\TypeCoverage\CollectorDataNormalizer;
use TomasVotruba\TypeCoverage\Collectors\PropertyTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Configuration;
use TomasVotruba\TypeCoverage\Formatter\TypeCoverageFormatter;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\PropertyTypeCoverageRule\PropertyTypeCoverageRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class PropertyTypeCoverageRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'missing property type';

    /**
     * @var string
     */
    public const TIP_MESSAGE = 'Out of %d possible property types, only %d - %.1f %% actually have it. Add more property types to get over %d %%';

    public function __construct(
        private readonly TypeCoverageFormatter $typeCoverageFormatter,
        private readonly Configuration $configuration,
        private readonly CollectorDataNormalizer $collectorDataNormalizer,
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
        if ($this->configuration->getRequiredPropertyTypeLevel() === 0) {
            return [];
        }

        $propertyTypeDeclarationCollector = $node->get(PropertyTypeDeclarationCollector::class);
        $typeCountAndMissingTypes = $this->collectorDataNormalizer->normalize($propertyTypeDeclarationCollector);

        return $this->typeCoverageFormatter->formatErrors(
            self::ERROR_MESSAGE,
            self::TIP_MESSAGE,
            $this->configuration->getRequiredPropertyTypeLevel(),
            $typeCountAndMissingTypes
        );
    }
}
