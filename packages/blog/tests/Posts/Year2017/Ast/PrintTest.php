<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2017\Ast;

use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use Symplify\SmartFileSystem\SmartFileSystem;
use TomasVotruba\Blog\Posts\Year2017\Ast\NodeVisitor\ChangeMethodNameNodeVisitor;
use TomasVotruba\Blog\Posts\Year2017\Ast\Printer\FormatPreservingPrinter;
use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class PrintTest extends TestCase implements PostTestInterface
{
    private string $srcDirectory;

    private FormatPreservingPrinter $formatPreservingPrinter;

    private SmartFileSystem $smartFileSystem;

    protected function setUp(): void
    {
        // helps BypassFinals for this test to keep "final" in the code
        stream_wrapper_restore('file');

        $this->srcDirectory = __DIR__ . '/../../../../src/Posts/Year2017/Ast';
        $this->formatPreservingPrinter = new FormatPreservingPrinter();
        $this->smartFileSystem = new SmartFileSystem();
    }

    protected function tearDown(): void
    {
        BypassFinals::enable();
    }

    public function testPrinter(): void
    {
        $fileContent = $this->smartFileSystem->readFile($this->srcDirectory . '/SomeClass.php');

        $newFileContent = $this->formatPreservingPrinter->traverseWithVisitorAndPrint(
            new ChangeMethodNameNodeVisitor(),
            $fileContent
        );

        $this->assertStringEqualsFile(__DIR__ . '/PrintSource/SomeClassModified.php.inc', $newFileContent);
    }

    public function getPostId(): int
    {
        return 63;
    }
}
