<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\ValueObject;

final class TypeCountAndMissingTypes
{
    /**
     * @param array<string, int[]> $missingTypeLinesByFilePath
     */
    public function __construct(
        private readonly int $totalCount,
        private readonly array $missingTypeLinesByFilePath
    ) {
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return array<string, int[]>
     */
    public function getMissingTypeLinesByFilePath(): array
    {
        return $this->missingTypeLinesByFilePath;
    }

    public function getCoveragePercentage(): int
    {
        return (int) (100 * ($this->getTypedCount() / $this->totalCount));
    }

    private function getTypedCount(): int
    {
        $missingCount = 0;

        foreach ($this->missingTypeLinesByFilePath as $missingTypeLines) {
            $missingCount += count($missingTypeLines);
        }

        return $this->totalCount - $missingCount;
    }
}
