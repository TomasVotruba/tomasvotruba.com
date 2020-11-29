<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Posts\Year2017\Ast\Printer;

use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\Parser\Php7;
use PhpParser\PrettyPrinter\Standard;

final class FormatPreservingPrinter
{
    public function traverseWithVisitorAndPrint(NodeVisitor $nodeVisitor, string $code): string
    {
        $emulative = $this->createLexer();

        $php7 = new Php7($emulative);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new CloningVisitor());

        $oldStmts = $php7->parse($code);
        $oldTokens = $emulative->getTokens();

        $newStmts = $traverser->traverse($oldStmts);

        // our code start

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($nodeVisitor);

        $traversedNodes = $nodeTraverser->traverse($newStmts);

        // our code end

        $standard = new Standard();
        $newStmts = $traversedNodes;
        return $standard->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
    }

    private function createLexer(): Emulative
    {
        return new Emulative([
            'usedAttributes' => ['comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos'],
        ]);
    }
}
