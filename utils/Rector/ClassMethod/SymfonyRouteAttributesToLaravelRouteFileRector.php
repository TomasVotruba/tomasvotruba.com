<?php

namespace TomasVotruba\Utils\Rector\ClassMethod;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @todo combine with collector + post Rector rule to dump the file
 */
final class SymfonyRouteAttributesToLaravelRouteFileRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        // TODO: Implement getRuleDefinition() method.
    }

    public function getNodeTypes(): array
    {
        return [Node\Stmt\ClassMethod::class];
    }

    /**
     * @param Node\Stmt\ClassMethod $node
     */
    public function refactor(Node $node)
    {
        dump($node);
        die;
    }
}
