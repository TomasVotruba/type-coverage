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
final class ReturnTypeCoverageRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible return types, only %d - %.1f %% actually have it. Add more return types to get over %d %%';

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

    /**
     * @readonly
     * @var \TomasVotruba\TypeCoverage\CollectorDataNormalizer
     */
    private $collectorDataNormalizer;

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
        $returnSeaLevelDataByFilePath = $node->get(ReturnTypeDeclarationCollector::class);
        $typeCountAndMissingTypes = $this->collectorDataNormalizer->normalize($returnSeaLevelDataByFilePath);

        return $this->typeCoverageFormatter->formatErrors(
            self::ERROR_MESSAGE,
            $this->configuration->getRequiredReturnTypeLevel(),
            $typeCountAndMissingTypes
        );
    }
}
