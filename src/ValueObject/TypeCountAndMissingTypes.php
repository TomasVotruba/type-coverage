<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\ValueObject;

final readonly class TypeCountAndMissingTypes
{
    /**
     * @param array<string, int[]> $missingTypeLinesByFilePath
     */
    public function __construct(
        private int $totalCount,
        private int $missingCount,
        private array $missingTypeLinesByFilePath
    ) {
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getFilledCount(): int
    {
        return $this->totalCount - $this->missingCount;
    }

    /**
     * @return array<string, int[]>
     */
    public function getMissingTypeLinesByFilePath(): array
    {
        return $this->missingTypeLinesByFilePath;
    }

    public function getCoveragePercentage(): float
    {
        return 100 * ($this->getTypedCount() / $this->totalCount);
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
