<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PHPStan\Collectors\Collector;
use TomasVotruba\TypeCoverage\Collectors\ConstantTypeDeclarationCollector;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\ConstantTypeCoverageRule\ConstantTypeCoverageRuleTest
 */
final class ConstantTypeCoverageRule extends AbstractTypeCoverageRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible constant types, only %d - %.1f %% actually have it. Add more constant types to get over %s %%';

    /**
     * @var string
     */
    private const IDENTIFIER = 'typeCoverage.constantTypeCoverage';

    /**
     * @return class-string<Collector>
     */
    protected function getCollectorClass(): string
    {
        return ConstantTypeDeclarationCollector::class;
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
        return 'Class constant type coverage is %.1f %% out of %d possible';
    }

    protected function getRequiredTypeLevel(): float
    {
        return (float) $this->configuration->getRequiredConstantTypeLevel();
    }

    protected function shouldSkip(): bool
    {
        // constant types are available only on PHP 8.3+
        return PHP_VERSION_ID < 80300;
    }

    protected function isEnabled(): bool
    {
        return $this->configuration->isConstantTypeCoverageEnabled();
    }
}
