<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2017\Ast;

use DG\BypassFinals;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\Posts\Year2017\Ast\NodeVisitor\ChangeMethodNameNodeVisitor;
use TomasVotruba\Website\Posts\Year2017\Ast\Printer\FormatPreservingPrinter;

final class PrintTest extends TestCase
{
    private string $srcDirectory;

    private FormatPreservingPrinter $formatPreservingPrinter;

    protected function setUp(): void
    {
        /** stops @see BypassFinals just for this test to keep "final" in the code */
        stream_wrapper_restore('file');

        $this->srcDirectory = __DIR__ . '/../../../../src/Posts/Year2017/Ast';
        $this->formatPreservingPrinter = new FormatPreservingPrinter();
    }

    protected function tearDown(): void
    {
        BypassFinals::enable();
    }

    public function testPrinter(): void
    {
        $fileContent = FileSystem::read($this->srcDirectory . '/SomeClass.php');

        $newFileContent = $this->formatPreservingPrinter->traverseWithVisitorAndPrint(
            new ChangeMethodNameNodeVisitor(),
            $fileContent
        );

        $this->assertStringEqualsFile(__DIR__ . '/PrintSource/SomeClassModified.php.inc', $newFileContent);
    }
}
