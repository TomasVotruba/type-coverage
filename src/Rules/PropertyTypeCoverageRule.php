<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
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
    public const ERROR_MESSAGE = 'Out of %d possible property types, only %d %% actually have it. Add more property types to get over %d %%';

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
        if ($this->configuration->getRequiredPropertyTypeLevel() === 0) {
            return [];
        }

        $propertyTypeDeclarationCollector = $node->get(PropertyTypeDeclarationCollector::class);

        $typedPropertyCount = 0;
        $propertyCount = 0;

        $printedUntypedPropertiesContents = [];

        foreach ($propertyTypeDeclarationCollector as $propertySeaLevelData) {
            foreach ($propertySeaLevelData as $nestedPropertySeaLevelData) {
                $typedPropertyCount += $nestedPropertySeaLevelData[0];
                $propertyCount += $nestedPropertySeaLevelData[1];

                if (! $this->configuration->shouldPrintSuggestions()) {
                    continue;
                }

                /** @var string $printedPropertyContent */
                $printedPropertyContent = $nestedPropertySeaLevelData[2];
                if ($printedPropertyContent !== '') {
                    $printedUntypedPropertiesContents[] = trim($printedPropertyContent);
                }
            }
        }

        return $this->typeCoverageFormatter->formatErrors(
            self::ERROR_MESSAGE,
            $this->configuration->getRequiredPropertyTypeLevel(),
            $propertyCount,
            $typedPropertyCount,
            $printedUntypedPropertiesContents
        );
    }
}
