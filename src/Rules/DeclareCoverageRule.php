<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use TomasVotruba\TypeCoverage\Collectors\DeclareCollector;
use TomasVotruba\TypeCoverage\Configuration;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\DeclareCoverageRule\DeclareCoverageRuleTest
 *
 * @implements Rule<CollectedDataNode>
 */
final readonly class DeclareCoverageRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible declare(strict_types=1), only %d - %.1f %% actually have it. Add more declares to get over %s %%';

    public function __construct(
        private Configuration $configuration,
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
        $requiredDeclareLevel = $this->configuration->getRequiredDeclareLevel();

        // not enabled
        if ($requiredDeclareLevel === 0) {
            return [];
        }

        $declareCollector = $node->get(DeclareCollector::class);
        $totalPossibleDeclares = count($declareCollector);

        // nothing to handle
        if ($totalPossibleDeclares === 0) {
            return [];
        }

        $coveredDeclares = 0;
        $notCoveredDeclareFilePaths = [];

        foreach ($declareCollector as $fileName => $data) {
            // has declares
            if ($data === [true]) {
                ++$coveredDeclares;
            } else {
                $notCoveredDeclareFilePaths[] = $fileName;
            }
        }

        $declareCoverage = ($coveredDeclares / $totalPossibleDeclares) * 100;

        // we meet the limit, all good
        if ($declareCoverage >= $requiredDeclareLevel) {
            return [];
        }

        $ruleErrors = [];
        foreach ($notCoveredDeclareFilePaths as $notCoveredDeclareFilePath) {
            $errorMessage = sprintf(
                self::ERROR_MESSAGE,
                $totalPossibleDeclares,
                $coveredDeclares,
                $declareCoverage,
                $requiredDeclareLevel,
            );

            $ruleErrors[] = RuleErrorBuilder::message($errorMessage)->file($notCoveredDeclareFilePath)->build();
        }

        return $ruleErrors;
    }
}
