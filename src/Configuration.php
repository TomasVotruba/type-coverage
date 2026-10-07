<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage;

final class Configuration
{
    /**
     * @var array<string, mixed>
     * @readonly
     */
    private array $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        array $parameters
    ) {
        $this->parameters = $parameters;
        if ($parameters['declare'] !== null) {
            fwrite(
                STDERR,
                'The "type_coverage.declare" parameter is deprecated and has no effect. Remove it from your config and use SafeDeclareStrictTypesRector from Rector instead.' . PHP_EOL
            );
        }

        if ($parameters['print_suggestions'] !== null) {
            fwrite(
                STDERR,
                'The "type_coverage.print_suggestions" parameter is deprecated and has no effect. Type coverage issues are always reported at their exact file and line. Remove it from your config.' . PHP_EOL
            );
        }
    }

    /**
     * @return float|int
     */
    public function getRequiredPropertyTypeLevel()
    {
        return $this->parameters['property'] ?? $this->parameters['property_type'];
    }

    public function isConstantTypeCoverageEnabled(): bool
    {
        // constant types are available only on PHP 8.3+
        if (PHP_VERSION_ID < 80300) {
            return false;
        }

        return $this->getRequiredConstantTypeLevel() > 0;
    }

    /**
     * @return float|int
     */
    public function getRequiredConstantTypeLevel()
    {
        return $this->parameters['constant'] ?? $this->parameters['constant_type'];
    }

    /**
     * @return float|int
     */
    public function getRequiredParamTypeLevel()
    {
        return $this->parameters['param'] ?? $this->parameters['param_type'];
    }

    /**
     * @return float|int
     */
    public function getRequiredReturnTypeLevel()
    {
        return $this->parameters['return'] ?? $this->parameters['return_type'];
    }

    public function showOnlyMeasure(): bool
    {
        return $this->parameters['measure'];
    }
}
