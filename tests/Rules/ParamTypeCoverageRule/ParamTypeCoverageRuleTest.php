<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TomasVotruba\TypeCoverage\Collectors\ParamTypeDeclarationCollector;
use TomasVotruba\TypeCoverage\Rules\ParamTypeCoverageRule;

final class ParamTypeCoverageRuleTest extends RuleTestCase
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
        yield [[__DIR__ . '/Fixture/SkipKnownParamType.php', __DIR__ . '/Fixture/SkipAgainKnownParamType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipVariadic.php'], []];
        yield [[__DIR__ . '/Fixture/SkipCallableParam.php'], []];

        $firstErrorMessage = sprintf(ParamTypeCoverageRule::ERROR_MESSAGE, 3, 1, 33.3, 80);
        $thirdErrorMessage = sprintf(ParamTypeCoverageRule::ERROR_MESSAGE, 3, 1, 33.3, 80);

        yield [[__DIR__ . '/Fixture/UnknownParamType.php'], [[$firstErrorMessage, 9], [$thirdErrorMessage, 13]]];
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
        return self::getContainer()->getByType(ParamTypeCoverageRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(ParamTypeDeclarationCollector::class)];
    }
}
