<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage;

final class Configuration
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private readonly array $parameters
    ) {
    }

    public function getRequiredPropertyTypeLevel(): int
    {
        return $this->parameters['property_type'];
    }

    public function getRequiredParamTypeLevel(): int
    {
        return $this->parameters['param_type'];
    }

    public function getRequiredReturnTypeLevel(): int
    {
        return $this->parameters['return_type'];
    }
}
