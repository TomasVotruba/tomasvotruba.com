<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2017\Ast;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\Posts\Year2017\Ast\NodeVisitor\ChangeMethodNameNodeVisitor;
use TomasVotruba\Website\Posts\Year2017\Ast\Printer\FormatPreservingPrinter;

final class PrintTest extends TestCase
{
    /**
     * @var string
     */
    private $srcDirectory;

    /**
     * @var FormatPreservingPrinter
     */
    private $formatPreservingPrinter;

    protected function setUp(): void
    {
        $this->srcDirectory = __DIR__ . '/../../../../src/Posts/Year2017/Ast';
        $this->formatPreservingPrinter = new FormatPreservingPrinter();
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
