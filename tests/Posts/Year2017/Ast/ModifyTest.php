<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2017\Ast;

use Nette\Utils\FileSystem;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\Posts\Year2017\Ast\NodeVisitor\ChangeMethodNameNodeVisitor;

final class ModifyTest extends TestCase
{
    private string $srcDirectory;

    private NodeTraverser $nodeTraverser;

    private Parser $parser;

    private NodeFinder $nodeFinder;

    protected function setUp(): void
    {
        $this->srcDirectory = __DIR__ . '/../../../../src/Posts/Year2017/Ast';
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        $this->nodeFinder = new NodeFinder();
        $this->nodeTraverser = new NodeTraverser();
    }

    public function testParse(): void
    {
        $nodes = $this->parser->parse(FileSystem::read($this->srcDirectory . '/SomeClass.php'));
        $this->assertNotSame([], $nodes);

        /** @var Namespace_[] $nodes */
        $classNode = $nodes[1]->stmts[1];
        $this->assertInstanceOf(Class_::class, $classNode);

        /** @var Class_ $classMethodNode */
        $classMethodNode = $classNode->stmts[0];
        $this->assertInstanceOf(ClassMethod::class, $classMethodNode);
    }

    public function testNodeVisitor(): void
    {
        $this->nodeTraverser->addVisitor(new ChangeMethodNameNodeVisitor());

        /** @var Node[] $nodes */
        $nodes = $this->parser->parse(FileSystem::read($this->srcDirectory . '/SomeClass.php'));

        /** @var ClassMethod $classMethodNode */
        $classMethodNode = $this->nodeFinder->findFirstInstanceOf($nodes, ClassMethod::class);
        $this->assertSame('someMethod', $classMethodNode->name->toString());

        $newNodes = $this->nodeTraverser->traverse($nodes);

        /** @var ClassMethod $classMethodNode */
        $classMethodNode = $this->nodeFinder->findFirstInstanceOf($newNodes, ClassMethod::class);
        $this->assertSame('changedName', $classMethodNode->name->toString());
    }
}
