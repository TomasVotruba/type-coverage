<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use TomasVotruba\TypeCoverage\Collectors\ClassLike\PropertyTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Formatter\TypeCoverageFormatter;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\PropertyTypeDeclarationSeaLevelRule\PropertyTypeDeclarationSeaLevelRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final class PropertyTypeCoverageRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible property types, only %d %% actually have it. Add more property types to get over %d %%';

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
        $propertySeaLevelDataByFilePath = $node->get(PropertyTypeDeclarationCollector::class);

        $typedPropertyCount = 0;
        $propertyCount = 0;

        $printedUntypedPropertiesContents = [];

        foreach ($propertySeaLevelDataByFilePath as $propertySeaLevelData) {
            foreach ($propertySeaLevelData as $nestedPropertySeaLevelData) {
                $typedPropertyCount += $nestedPropertySeaLevelData[0];
                $propertyCount += $nestedPropertySeaLevelData[1];

                if (! $this->printSuggestions) {
                    continue;
                }

                /** @var string $printedPropertyContent */
                $printedPropertyContent = $nestedPropertySeaLevelData[2];
                if ($printedPropertyContent !== '') {
                    $printedUntypedPropertiesContents[] = trim($printedPropertyContent);
                }
            }
        }

        return $this->seaLevelRuleErrorFormatter->formatErrors(
            self::ERROR_MESSAGE,
            $this->minimalLevel,
            $propertyCount,
            $typedPropertyCount,
            $printedUntypedPropertiesContents
        );
    }
}
