<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage;

final readonly class Configuration
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private array $parameters
    ) {
    }

    public function getRequiredPropertyTypeLevel(): float|int
    {
        return $this->parameters['property_type'];
    }

    public function getRequiredParamTypeLevel(): float|int
    {
        return $this->parameters['param_type'];
    }

    public function getRequiredReturnTypeLevel(): float|int
    {
        return $this->parameters['return_type'];
    }
}
