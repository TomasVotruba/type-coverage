<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage;

final class Configuration
{
    /**
     * @var array<string, mixed>
     * @readonly
     */
    private $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return float|int
     */
    public function getRequiredPropertyTypeLevel()
    {
        return $this->parameters['property'] ?? $this->parameters['property_type'];
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
}
