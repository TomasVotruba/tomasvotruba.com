<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2017\Ast;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\Posts\Year2017\Ast\NodeVisitor\ChangeMethodNameNodeVisitor;

final class ModifyTest extends TestCase
{
    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var string
     */
    private $srcDirectory;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    protected function setUp(): void
    {
        $this->srcDirectory = __DIR__ . '/../../../../src/Posts/Year2017/Ast';
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $this->nodeFinder = new NodeFinder;
        $this->nodeTraverser = new NodeTraverser;
    }

    public function testParse(): void
    {
        $nodes = $this->parser->parse(file_get_contents($this->srcDirectory . '/SomeClass.php'));

        $this->assertNotSame([], $nodes);

        $classNode = $nodes[1]->stmts[0];
        $this->assertInstanceOf(Class_::class, $classNode);

        $classMethodNode = $classNode->stmts[0];
        $this->assertInstanceOf(ClassMethod::class, $classMethodNode);
    }

    public function testNodeVisitor(): void
    {
        $this->nodeTraverser->addVisitor(new ChangeMethodNameNodeVisitor);

        $nodes = $this->parser->parse(file_get_contents($this->srcDirectory . '/SomeClass.php'));
        /** @var ClassMethod $classMethodNode */
        $classMethodNode = $this->nodeFinder->findFirstInstanceOf($nodes, ClassMethod::class);
        $this->assertSame('someMethod', $classMethodNode->name->toString());

        $newNodes = $this->nodeTraverser->traverse($nodes);
        /** @var ClassMethod $classMethodNode */
        $classMethodNode = $this->nodeFinder->findFirstInstanceOf($newNodes, ClassMethod::class);
        $this->assertSame('changedName', $classMethodNode->name->toString());
    }
}
