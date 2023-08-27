<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Formatter;

use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use TomasVotruba\TypeCoverage\ValueObject\TypeCountAndMissingTypes;

final class TypeCoverageFormatter
{
    /**
     * @return RuleError[]
     */
    public function formatErrors(
        string $message,
        int $minimalLevel,
        TypeCountAndMissingTypes $typeCountAndMissingTypes
    ): array {
        if ($typeCountAndMissingTypes->getTotalCount() === 0) {
            return [];
        }

        $typeCoveragePercentage = $typeCountAndMissingTypes->getCoveragePercentage();

        // has the code met the minimal sea level of types?
        if ($typeCoveragePercentage >= $minimalLevel) {
            return [];
        }

        $ruleErrors = [];

        foreach ($typeCountAndMissingTypes->getMissingTypeLinesByFilePath() as $filePath => $lines) {
            $errorMessage = sprintf(
                $message,
                $typeCountAndMissingTypes->getTotalCount(),
                $typeCountAndMissingTypes->getFilledCount(),
                $typeCoveragePercentage,
                $minimalLevel
            );

            foreach ($lines as $line) {
                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                    ->file($filePath)
                    ->line($line)
                    ->build();
            }
        }

        return $ruleErrors;
    }
}
