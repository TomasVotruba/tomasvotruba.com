<?php

namespace TomasVotruba\Utils\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
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

    /**
     * @return array<class-string<\PhpParser\Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node)
    {
        if ($node->attrGroups === []) {
            return null;
        }

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if (! $this->isName($attr->name, 'Symfony\Component\Routing\Annotation\Route')) {
                    continue;
                }

                dump($attr->args);
                die;
            }
        }

        die;
    }
}
