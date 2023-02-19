<?php

namespace TomasVotruba\Utils\Rector\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use TomasVotruba\Utils\Rector\DataCollector\RouteDataCollector;

/**
 * @todo combine with collector + post Rector rule to dump the file
 */
final class SymfonyRouteAttributesToLaravelRouteFileRector extends AbstractRector
{
    public function __construct(
        private readonly RouteDataCollector $routeDataCollector,
    ) {
    }

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

        $hasChanged = false;

        foreach ($node->attrGroups as $key => $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if (! $this->isName($attribute->name, 'Symfony\Component\Routing\Annotation\Route')) {
                    continue;
                }

                foreach ($attribute->args as $arg) {
                    dump($arg);
                    die;

                    if ($arg->name instanceof Identifier) {
                        dump($this->getName($arg->name));
                    }
                }

                // remove :)
                unset($node->attrGroups[$key]);

                die;

                $hasChanged = true;

                $this->routeDataCollector->addData($attribute->args);
            }
        }

        if ($hasChanged) {
            return $node;
        }

        return null;
    }
}
