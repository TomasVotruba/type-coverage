<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Rules;

use PHPStan\Collectors\Collector;
use TomasVotruba\TypeCoverage\Collectors\ParamTypeDeclarationCollector;

/**
 * @see \TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\ParamTypeCoverageRuleTest
 */
final class ParamTypeCoverageRule extends AbstractTypeCoverageRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Out of %d possible param types, only %d - %.1f %% actually have it. Add more param types to get over %s %%';

    /**
     * @var string
     */
    private const IDENTIFIER = 'typeCoverage.paramTypeCoverage';

    /**
     * @return class-string<Collector>
     */
    protected function getCollectorClass(): string
    {
        return ParamTypeDeclarationCollector::class;
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
        return 'Param type coverage is %.1f %% out of %d possible';
    }

    protected function getRequiredTypeLevel(): float
    {
        return (float) $this->configuration->getRequiredParamTypeLevel();
    }
}
