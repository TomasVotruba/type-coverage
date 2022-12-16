<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\PropertyTypeDeclarationSeaLevelRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use TomasVotruba\TypeCoverage\Collectors\ClassLike\PropertyTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Rules\PropertyTypeCoverageRule;

/**
 * @extends RuleTestCase<PropertyTypeCoverageRule>
 */
final class PropertyTypeCoverageRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     *
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipKnownPropertyType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipCallableProperty.php'], []];
        yield [[__DIR__ . '/Fixture/SkipResource.php'], []];

        $errorMessage = sprintf(PropertyTypeDeclarationSeaLevelRule::ERROR_MESSAGE, 2, 0, 80);

        $errorMessage .= '

public $name;

public $surname;
';

        yield [[__DIR__ . '/Fixture/UnknownPropertyType.php'], [[$errorMessage, -1]]];
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
