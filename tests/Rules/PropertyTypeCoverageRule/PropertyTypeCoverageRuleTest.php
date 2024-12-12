<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\PropertyTypeCoverageRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\TypeCoverage\Collectors\PropertyTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Rules\PropertyTypeCoverageRule;

final class PropertyTypeCoverageRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param list<array{0: string, 1: int, 2?: string|null}> $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipKnownPropertyType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipCallableProperty.php'], []];
        yield [[__DIR__ . '/Fixture/SkipResource.php'], []];
        yield [[__DIR__ . '/Fixture/SkipParentBlockingProperty.php'], []];

        $errorMessage = sprintf(PropertyTypeCoverageRule::ERROR_MESSAGE, 2, 1, 50.0, 80);
        yield [[__DIR__ . '/Fixture/UnknownPropertyType.php'], [[$errorMessage, 9]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(PropertyTypeCoverageRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(PropertyTypeDeclarationCollector::class)];
    }
}
