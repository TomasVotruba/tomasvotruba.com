<?php declare(strict_types=1);

namespace TomasVotruba\Website\Posts\Year2017\Ast\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;

final class ChangeMethodNameNodeVisitor extends NodeVisitorAbstract
{
    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof ClassMethod) {
            return $node;
        }

        $node->name = new Identifier('changedName');

        return $node;
    }
}
