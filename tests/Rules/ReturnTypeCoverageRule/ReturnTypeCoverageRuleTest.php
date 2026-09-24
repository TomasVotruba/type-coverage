<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ReturnTypeCoverageRule;

use Iterator;
use Override;
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

    public function testTraitReturnTypeIsCountedOncePerDeclaration(): void
    {
        $traitFile = __DIR__ . '/Source/TraitWithMissingReturnType.php';

        $errors = $this->gatherAnalyserErrors([
            __DIR__ . '/Fixture/ClassUsingTrait.php',
            __DIR__ . '/Fixture/SecondClassUsingTrait.php',
            $traitFile,
        ]);

        $this->assertCount(1, $errors);
        $this->assertSame($traitFile, $errors[0]->getFile());
        $this->assertStringContainsString('Out of 1 possible return types', $errors[0]->getMessage());
    }

    public function testDistinctTraitMembersAreCountedSeparately(): void
    {
        $traitFile = __DIR__ . '/Source/MultiMemberTrait.php';

        $errors = $this->gatherAnalyserErrors([
            __DIR__ . '/Fixture/ClassUsingMultiMemberTrait.php',
            __DIR__ . '/Fixture/SecondClassUsingMultiMemberTrait.php',
            $traitFile,
        ]);

        // the trait declares 3 methods and is used by 2 classes; each method counts once,
        // including the typed one, and the 2 untyped ones are reported separately
        $this->assertCount(2, $errors);

        $this->assertSame($traitFile, $errors[0]->getFile());
        $this->assertSame(9, $errors[0]->getLine());

        $this->assertSame($traitFile, $errors[1]->getFile());
        $this->assertSame(14, $errors[1]->getLine());

        $this->assertStringContainsString('Out of 3 possible return types, only 1', $errors[0]->getMessage());
    }

    public function testMembersOfDifferentTraitsOnTheSameLineStayDistinct(): void
    {
        $alphaTraitFile = __DIR__ . '/Source/AlphaSameLineTrait.php';
        $omegaTraitFile = __DIR__ . '/Source/OmegaSameLineTrait.php';

        $errors = $this->gatherAnalyserErrors([
            __DIR__ . '/Fixture/ClassUsingBothSameLineTraits.php',
            __DIR__ . '/Fixture/SecondClassUsingBothSameLineTraits.php',
            $alphaTraitFile,
            $omegaTraitFile,
        ]);

        // both traits are byte-identical apart from their name, so their method shares
        // a line and a start position: the trait file is what tells the two apart
        $this->assertCount(2, $errors);
        $this->assertStringContainsString('Out of 2 possible return types', $errors[0]->getMessage());

        $errorFiles = [];
        foreach ($errors as $error) {
            $errorFiles[] = $error->getFile();
            $this->assertSame(9, $error->getLine());
        }

        sort($errorFiles);
        $this->assertSame([$alphaTraitFile, $omegaTraitFile], $errorFiles);
    }

    public function testPlainClassMembersOnTheSameLineAreNotDeduplicated(): void
    {
        $alphaFile = __DIR__ . '/Fixture/AlphaPlainUntypedMethod.php';
        $omegaFile = __DIR__ . '/Fixture/OmegaPlainUntypedMethod.php';

        $errors = $this->gatherAnalyserErrors([$alphaFile, $omegaFile]);

        // the two classes are byte-identical apart from their name, so their method
        // shares a line and a start position; no trait is involved, so nothing may collapse
        $this->assertCount(2, $errors);
        $this->assertStringContainsString('Out of 2 possible return types', $errors[0]->getMessage());

        $errorFiles = [];
        foreach ($errors as $error) {
            $errorFiles[] = $error->getFile();
        }

        sort($errorFiles);
        $this->assertSame([$alphaFile, $omegaFile], $errorFiles);
    }

    public function testTwoTraitMembersOnOneLineStayDistinct(): void
    {
        $traitFile = __DIR__ . '/Source/TwoMethodsOnOneLineTrait.php';

        $errors = $this->gatherAnalyserErrors([
            __DIR__ . '/Fixture/ClassUsingTwoMethodsOnOneLineTrait.php',
            __DIR__ . '/Fixture/SecondClassUsingTwoMethodsOnOneLineTrait.php',
            $traitFile,
        ]);

        // the trait declares both methods on line 9, so the line cannot identify
        // a declaration; both must still be counted and reported
        $this->assertCount(2, $errors);
        $this->assertStringContainsString('Out of 2 possible return types', $errors[0]->getMessage());

        foreach ($errors as $error) {
            $this->assertSame($traitFile, $error->getFile());
            $this->assertSame(9, $error->getLine());
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
        return self::getContainer()->getByType(ReturnTypeCoverageRule::class);
    }

    /**
     * @return Collector[]
     */
    #[Override]
    protected function getCollectors(): array
    {
        return [self::getContainer()->getByType(ReturnTypeDeclarationCollector::class)];
    }
}
