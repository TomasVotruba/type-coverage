<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Configuration;

use PHPStan\Analyser\LazyInternalScopeFactory;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\MemoizingContainer;
use PHPStan\DependencyInjection\Nette\NetteContainer;
use ReflectionProperty;
use TomasVotruba\TypeCoverage\Configuration;

/**
 * The easiest way to reach project configuration from Scope object
 */
final class ScopeConfigurationResolver
{
    private static ?bool $areFullPathsAnalysed = null;

    private static function areFullPathsAnalysed(Scope $scope): bool
    {
        // cache for speed
        if (self::$areFullPathsAnalysed !== null) {
            return self::$areFullPathsAnalysed;
        }

        $scopeFactory = self::getPrivateProperty($scope, 'scopeFactory');

        // different types are used in tests, there we want to always analyse everything
        if (! $scopeFactory instanceof LazyInternalScopeFactory) {
            return true;
        }

        $scopeFactoryContainer = self::getPrivateProperty($scopeFactory, 'container');
        if (! $scopeFactoryContainer instanceof MemoizingContainer) {
            // edge case, unable to analyse
            return true;
        }

        /** @var NetteContainer $originalContainer */
        $originalContainer = self::getPrivateProperty($scopeFactoryContainer, 'originalContainer');

        $analysedPaths = $originalContainer->getParameter('analysedPaths');
        $analysedPathsFromConfig = $originalContainer->getParameter('analysedPathsFromConfig');

        self::$areFullPathsAnalysed = $analysedPathsFromConfig === $analysedPaths;

        return self::$areFullPathsAnalysed;
    }

    public static function shouldSkipPartialPathsAnalysis(Scope $scope, Configuration $configuration): bool
    {
        // If allow_partial_paths is enabled, don't skip analysis even for partial paths
        if ($configuration->allowPartialPaths()) {
            return false;
        }

        // skip if not full paths
        return ! self::areFullPathsAnalysed($scope);
    }

    private static function getPrivateProperty(object $object, string $propertyName): object
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);

        return $reflectionProperty->getValue($object);
    }
}
