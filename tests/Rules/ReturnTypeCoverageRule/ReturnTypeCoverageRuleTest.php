<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\TypeCoverage\Collectors\ReturnTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Rules\ReturnTypeCoverageRule;

final class ReturnTypeCoverageRuleTest extends RuleTestCase
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

    /**
     * @return Iterator<array<array<int, array<int, mixed>>, mixed>>
     */
    public static function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipKnownReturnType.php', __DIR__ . '/Fixture/SkipAgainKnownReturnType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipConstructor.php'], []];
        yield [[__DIR__ . '/Fixture/SkipTraitConstructor.php'], []];

        $errorMessage = sprintf(ReturnTypeCoverageRule::ERROR_MESSAGE, 2, 0, 0, 80);
        yield [[__DIR__ . '/Fixture/UnknownReturnType.php'], [[$errorMessage, 9], [$errorMessage, 13]]];
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
