<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule;

use Iterator;
use Override;
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
        yield [[__DIR__ . '/Fixture/SkipParentMethodOverride.php'], []];

        $firstErrorMessage = sprintf(ParamTypeCoverageRule::ERROR_MESSAGE, 3, 1, 33.3, 80);
        $thirdErrorMessage = sprintf(ParamTypeCoverageRule::ERROR_MESSAGE, 3, 1, 33.3, 80);

        yield [[__DIR__ . '/Fixture/UnknownParamType.php'], [[$firstErrorMessage, 9], [$thirdErrorMessage, 13]]];
    }

    public function testErrorInTraitIsReportedAtTraitFile(): void
    {
        $classFile = __DIR__ . '/Fixture/ClassUsingTrait.php';
        $traitFile = __DIR__ . '/Source/TraitWithMissingParamType.php';

        $errors = $this->gatherAnalyserErrors([$classFile, $traitFile]);

        $this->assertCount(1, $errors);

        $error = $errors[0];
        $this->assertSame($traitFile, $error->getFile());
        $this->assertSame(9, $error->getLine());
    }

    public function testTraitParamIsCountedOncePerDeclaration(): void
    {
        $errors = $this->gatherAnalyserErrors([
            __DIR__ . '/Fixture/ClassUsingTrait.php',
            __DIR__ . '/Fixture/SecondClassUsingTrait.php',
            __DIR__ . '/Source/TraitWithMissingParamType.php',
        ]);

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('Out of 1 possible param types', $errors[0]->getMessage());
    }

    public function testFunctionLikesSharingALineInATraitStayDistinct(): void
    {
        $traitFile = __DIR__ . '/Source/TraitWithArrowFunctionsOnOneLine.php';

        $errors = $this->gatherAnalyserErrors([
            __DIR__ . '/Fixture/ClassUsingArrowFunctionTrait.php',
            $traitFile,
        ]);

        // the two arrow functions share line 15, so the line cannot identify a
        // declaration; both untyped params must survive, even with one using class
        $this->assertCount(2, $errors);
        $this->assertStringContainsString('Out of 3 possible param types, only 1', $errors[0]->getMessage());

        foreach ($errors as $error) {
            $this->assertSame($traitFile, $error->getFile());
            $this->assertSame(15, $error->getLine());
        }
    }

    /**
     * @return string[]
     */
    #[Override]
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
    #[Override]
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(ParamTypeDeclarationCollector::class)];
    }
}
