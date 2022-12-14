<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use TomasVotruba\TypeCoverage\Collectors\ReturnTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Rules\ReturnTypeCoverageRule;

/**
 * @extends RuleTestCase<ReturnTypeCoverageRule>
 */
final class ReturnTypeCoverageRuleTest extends RuleTestCase
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
        yield [[__DIR__ . '/Fixture/SkipKnownReturnType.php', __DIR__ . '/Fixture/SkipAgainKnownReturnType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipConstructor.php'], []];

        $errorMessage = sprintf(ReturnTypeCoverageRule::ERROR_MESSAGE, 2, 0, 80);
        $errorMessage .= '

public function run()
{
}

public function again()
{
}
';

        yield [[__DIR__ . '/Fixture/UnknownReturnType.php'], [[$errorMessage, -1]]];
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
        return self::getContainer()->getByType(ReturnTypeCoverageRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(ReturnTypeDeclarationCollector::class)];
    }
}
