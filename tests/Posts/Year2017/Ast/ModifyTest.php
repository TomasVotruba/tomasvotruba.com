<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2017\Ast;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

final class ModifyTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var string
     */
    private $srcDirectory;

    protected function setUp(): void
    {
        $this->srcDirectory = __DIR__ . '/../../../../src/Posts/Year2017/Ast';
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
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
}
