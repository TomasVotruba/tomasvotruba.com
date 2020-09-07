<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2017\Ast;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Symplify\SmartFileSystem\SmartFileSystem;
use TomasVotruba\Blog\Posts\Year2017\Ast\NodeVisitor\ChangeMethodNameNodeVisitor;
use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class ModifyTest extends TestCase implements PostTestInterface
{
    private string $srcDirectory;

    private NodeTraverser $nodeTraverser;

    private Parser $parser;

    private NodeFinder $nodeFinder;

    private string $someClassFileContent;

    protected function setUp(): void
    {
        $this->srcDirectory = __DIR__ . '/../../../../src/Posts/Year2017/Ast';
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        $this->nodeFinder = new NodeFinder();
        $this->nodeTraverser = new NodeTraverser();

        $smartFileSystem = new SmartFileSystem();
        $this->someClassFileContent = $smartFileSystem->readFile($this->srcDirectory . '/SomeClass.php');
    }

    public function testParse(): void
    {
        $nodes = $this->parser->parse($this->someClassFileContent);
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
        $nodes = $this->parser->parse($this->someClassFileContent);

        /** @var ClassMethod $classMethodNode */
        $classMethodNode = $this->nodeFinder->findFirstInstanceOf($nodes, ClassMethod::class);
        $this->assertSame('someMethod', $classMethodNode->name->toString());

        $newNodes = $this->nodeTraverser->traverse($nodes);

        /** @var ClassMethod $classMethodNode */
        $classMethodNode = $this->nodeFinder->findFirstInstanceOf($newNodes, ClassMethod::class);
        $this->assertSame('changedName', $classMethodNode->name->toString());
    }

    public function getPostId(): int
    {
        return 63;
    }
}
