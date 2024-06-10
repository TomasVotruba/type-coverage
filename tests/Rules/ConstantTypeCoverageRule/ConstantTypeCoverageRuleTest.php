<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ConstantTypeCoverageRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\TypeCoverage\Collectors\ConstantTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Rules\ConstantTypeCoverageRule;

final class ConstantTypeCoverageRuleTest extends RuleTestCase
{

    public static function setUpBeforeClass(): void
    {
        if (PHP_VERSION_ID < 80300) {
            self::markTestSkipped('not working under 8.3');
        }
    }

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
        echo __METHOD__;
        yield [[__DIR__ . '/Fixture/SkipKnownConstantType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipParentBlockingConstant.php'], []];

        $errorMessage = sprintf(ConstantTypeCoverageRule::ERROR_MESSAGE, 2, 1, 50.0, 80);
        yield [[__DIR__ . '/Fixture/UnknownConstantType.php'], [[$errorMessage, 9]]];
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
        return self::getContainer()->getByType(ConstantTypeCoverageRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(ConstantTypeDeclarationCollector::class)];
    }
}
