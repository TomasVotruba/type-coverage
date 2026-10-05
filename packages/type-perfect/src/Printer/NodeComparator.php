<?php

declare(strict_types=1);

namespace Rector\TypePerfect\Printer;

use PhpParser\Node;
use PHPStan\Node\Printer\Printer;

final class NodeComparator
{
    /**
     * @readonly
     */
    private Printer $printer;

    public function __construct(Printer $printer)
    {
        $this->printer = $printer;
    }

    public function areNodesEqual(Node $firstNode, Node $secondNode): bool
    {
        // remove comments from nodes
        $firstNode->setAttribute('comments', null);
        $secondNode->setAttribute('comments', null);

        return $this->printer->prettyPrint([$firstNode]) === $this->printer->prettyPrint([$secondNode]);
    }
}
