<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\DeclareCoverageRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\TypeCoverage\Collectors\DeclareCollector;
use TomasVotruba\TypeCoverage\Rules\DeclareCoverageRule;

final class DeclareCoverageRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorsWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    public static function provideData(): Iterator
    {
        $expectedErrorMessage = sprintf(DeclareCoverageRule::ERROR_MESSAGE, 1, 0, 0.0, 50);
        yield [[__DIR__ . '/Fixture/SomeFileWithoutDeclares.php'], [[$expectedErrorMessage, -1]]];
        yield [[__DIR__ . '/Fixture/SkipDeclareTicks.php'], [[$expectedErrorMessage, -1]]];
        yield [[__DIR__ . '/Fixture/SkipDeclareStrictTypesZero.php'], [[$expectedErrorMessage, -1]]];

        yield [[__DIR__ . '/Fixture/DeclareCovered.php', __DIR__ . '/Fixture/SomeFileWithoutDeclares.php'], []];
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
        return self::getContainer()->getByType(DeclareCoverageRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(DeclareCollector::class)];
    }
}
