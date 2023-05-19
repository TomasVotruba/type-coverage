<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage;

use TomasVotruba\TypeCoverage\ValueObject\TypeCountAndMissingTypes;

final class CollectorDataNormalizer
{
    /**
     * @param mixed[] $collectorDataByPath
     */
    public function normalize(array $collectorDataByPath): TypeCountAndMissingTypes
    {
        $totalCount = 0;

        $missingTypeLinesByFilePath = [];

        foreach ($collectorDataByPath as $filePath => $returnSeaLevelData) {
            foreach ($returnSeaLevelData as $nestedData) {
                $totalCount += $nestedData[0];

                $missingTypeLinesByFilePath[$filePath] = array_merge(
                    $missingTypeLinesByFilePath[$filePath] ?? [],
                    $nestedData[1]
                );
            }
        }

        return new TypeCountAndMissingTypes($totalCount, $missingTypeLinesByFilePath);
    }
}
