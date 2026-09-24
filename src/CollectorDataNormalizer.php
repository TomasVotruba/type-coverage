<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage;

use TomasVotruba\TypeCoverage\ValueObject\TypeCountAndMissingTypes;

final class CollectorDataNormalizer
{
    /**
     * @param array<string, array<array{0: int, 1: array<int, int>, 2?: string|null, 3?: int}>> $collectorDataByPath
     */
    public function normalize(array $collectorDataByPath): TypeCountAndMissingTypes
    {
        $totalCount = 0;
        $missingCount = 0;

        $missingTypeLinesByFilePath = [];
        $seenTraitDeclarations = [];

        foreach ($collectorDataByPath as $filePath => $typeCoverageData) {
            foreach ($typeCoverageData as $nestedData) {
                $traitFilePath = $nestedData[2] ?? null;

                // a trait member is collected once per using class, so count it
                // once per declaration instead of once per class that uses the trait;
                // the start position identifies the declaration, as a line can hold several.
                // without a position, keep counting as before rather than risk dropping a declaration
                if ($traitFilePath !== null && isset($nestedData[3])) {
                    $traitDeclarationKey = $traitFilePath . ':' . $nestedData[3];
                    if (isset($seenTraitDeclarations[$traitDeclarationKey])) {
                        continue;
                    }

                    $seenTraitDeclarations[$traitDeclarationKey] = true;
                }

                $totalCount += $nestedData[0];

                $missingCount += count($nestedData[1]);

                // if the node is from a trait, route the error to the trait file
                // instead of the using-class file, so lines match the actual source
                $effectiveFilePath = $traitFilePath ?? $filePath;

                $missingTypeLinesByFilePath[$effectiveFilePath] = array_merge(
                    $missingTypeLinesByFilePath[$effectiveFilePath] ?? [],
                    $nestedData[1]
                );
            }
        }

        return new TypeCountAndMissingTypes($totalCount, $missingCount, $missingTypeLinesByFilePath);
    }
}
