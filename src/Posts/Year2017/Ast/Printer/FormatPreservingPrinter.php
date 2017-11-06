<?php declare(strict_types=1);

namespace TomasVotruba\Website\Posts\Year2017\Ast\Printer;

use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\Parser\Php7;
use PhpParser\PrettyPrinter;
use PhpParser\PrettyPrinter\Standard;

final class FormatPreservingPrinter
{
    public function traverseWithVisitorAndPrint(NodeVisitor $nodeVisitor, string $code): string
    {
        $lexer = new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);

        $parser = new Php7($lexer);
        $traverser = new NodeTraverser;
        $traverser->addVisitor(new CloningVisitor);

        $oldStmts = $parser->parse($code);
        $oldTokens = $lexer->getTokens();

        $newStmts = $traverser->traverse($oldStmts);

        // our code start

        $nodeTraverser = new NodeTraverser;
        $nodeTraverser->addVisitor($nodeVisitor);

        $newStmts = $traversedNodes = $nodeTraverser->traverse($newStmts);

        // our code end

        return (new Standard)->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
    }
}
