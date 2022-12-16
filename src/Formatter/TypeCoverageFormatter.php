<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Formatter;

use Nette\Utils\Strings;

final class TypeCoverageFormatter
{
    /**
     * @param string[] $errors
     * @return string[]
     */
    public function formatErrors(
        string $message,
        int $minimalLevel,
        int $propertyCount,
        int $typedPropertyCount,
        array $errors
    ): array {
        if ($propertyCount === 0) {
            return [];
        }

        $propertyTypeDeclarationSeaLevel = 100 * ($typedPropertyCount / $propertyCount);

        // has the code met the minimal sea level of types?
        if ($propertyTypeDeclarationSeaLevel >= $minimalLevel) {
            return [];
        }

        $errorMessage = sprintf($message, $propertyCount, $propertyTypeDeclarationSeaLevel, $minimalLevel);

        if ($errors !== []) {
            $errorMessage .= PHP_EOL . PHP_EOL;
            $errorMessage .= implode(PHP_EOL . PHP_EOL, $errors);
            $errorMessage .= PHP_EOL;

            // keep error printable
            $errorMessage = Strings::truncate($errorMessage, 8000);
        }

        return [$errorMessage];
    }
}
