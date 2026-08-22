<?php

declare(strict_types=1);

namespace TomasVotruba\TypeCoverage\Tests\Rules\ParamTypeCoverageRule\Source;

trait TraitWithArrowFunctionsOnOneLine
{
    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function filterItems(array $items): array
    {
        return array_map(fn ($value) => $value, array_filter($items, fn ($item) => (bool) $item));
    }
}
