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

    public function shouldPrintSuggestions(): bool
    {
        return $this->parameters['print_suggestions'];
    }
}
