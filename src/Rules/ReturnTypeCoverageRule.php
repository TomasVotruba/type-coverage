<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PHPStan\Collectors\Collector;
use TomasVotruba\TypeCoverage\Collectors\ReturnTypeDeclarationCollector;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule\ReturnTypeCoverageRuleTest
 */
final class ReturnTypeCoverageRule extends AbstractTypeCoverageRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible return types, only %d - %.1f %% actually have it. Add more return types to get over %s %%';

    /**
     * @var string
     */
    private const IDENTIFIER = 'typeCoverage.returnTypeCoverage';

    /**
     * @return class-string<Collector>
     */
    protected function getCollectorClass(): string
    {
        return ReturnTypeDeclarationCollector::class;
    }

    protected function getErrorMessage(): string
    {
        return self::ERROR_MESSAGE;
    }

    protected function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    protected function getMeasureMessage(): string
    {
        return 'Return type coverage is %.1f %% out of %d possible';
    }

    protected function getRequiredTypeLevel(): float
    {
        return (float) $this->configuration->getRequiredReturnTypeLevel();
    }
}
