<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PHPStan\Collectors\Collector;
use TomasVotruba\TypeCoverage\Collectors\PropertyTypeDeclarationCollector;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\PropertyTypeCoverageRule\PropertyTypeCoverageRuleTest
 */
final class PropertyTypeCoverageRule extends AbstractTypeCoverageRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible property types, only %d - %.1f %% actually have it. Add more property types to get over %s %%';

    /**
     * @var string
     */
    private const IDENTIFIER = 'typeCoverage.propertyTypeCoverage';

    /**
     * @return class-string<Collector>
     */
    protected function getCollectorClass(): string
    {
        return PropertyTypeDeclarationCollector::class;
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
        return 'Property type coverage is %.1f %% out of %d possible';
    }

    protected function getRequiredTypeLevel(): float
    {
        return (float) $this->configuration->getRequiredPropertyTypeLevel();
    }
}
