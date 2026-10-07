<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage;

use TomasVotruba\TypeCoverage\ValueObject\TypeCountAndMissingTypes;

final class CollectorDataNormalizer
{
    /**
     * @param array<string, array<array{count: int, missingLines: array<int, int>, traitFilePath?: string|null, startFilePos?: int}>> $collectorDataByPath
     */
    public function normalize(array $collectorDataByPath): TypeCountAndMissingTypes
    {
        $totalCount = 0;
        $missingCount = 0;

        $missingTypeLinesByFilePath = [];
        $seenTraitDeclarations = [];

        foreach ($collectorDataByPath as $filePath => $typeCoverageData) {
            foreach ($typeCoverageData as $nestedData) {
                $traitFilePath = $nestedData['traitFilePath'] ?? null;

                // a trait member is collected once per using class, so count it
                // once per declaration instead of once per class that uses the trait;
                // the start position identifies the declaration, as a line can hold several.
                // without a position, keep counting as before rather than risk dropping a declaration
                if ($traitFilePath !== null && isset($nestedData['startFilePos'])) {
                    $traitDeclarationKey = $traitFilePath . ':' . $nestedData['startFilePos'];
                    if (isset($seenTraitDeclarations[$traitDeclarationKey])) {
                        continue;
                    }

                    $seenTraitDeclarations[$traitDeclarationKey] = true;
                }

                $totalCount += $nestedData['count'];

                $missingCount += count($nestedData['missingLines']);

                // if the node is from a trait, route the error to the trait file
                // instead of the using-class file, so lines match the actual source
                $effectiveFilePath = $traitFilePath ?? $filePath;

                $missingTypeLinesByFilePath[$effectiveFilePath] = array_merge(
                    $missingTypeLinesByFilePath[$effectiveFilePath] ?? [],
                    $nestedData['missingLines']
                );
            }
        }

        return new TypeCountAndMissingTypes($totalCount, $missingCount, $missingTypeLinesByFilePath);
    }
}
