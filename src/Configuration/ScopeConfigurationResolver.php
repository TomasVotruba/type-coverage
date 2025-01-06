<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Configuration;

use PHPStan\Analyser\LazyInternalScopeFactory;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\MemoizingContainer;
use PHPStan\DependencyInjection\Nette\NetteContainer;
use ReflectionProperty;

/**
 * The easiest way to reach project configuration from Scope object
 */
final class ScopeConfigurationResolver
{
    public static function areFullPathsAnalysed(Scope $scope): bool
    {
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

        return $analysedPathsFromConfig === $analysedPaths;
    }

    private static function getPrivateProperty(object $object, string $propertyName): object
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);

        return $reflectionProperty->getValue($object);
    }
}
