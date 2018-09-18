<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\Exceptions;

use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use SplFileInfo as NativeSplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class RelativePathTest extends TestCase
{
    public function testSplFileInfo(): void
    {
        $splFileInfo = new NativeSplFileInfo('tests/Posts/Year2018/Exceptions/Source/some_file.txt');

        // is relative
        $this->assertSame('tests/Posts/Year2018/Exceptions/Source/some_file.txt', $splFileInfo->getPathname());

        // is absolute
        $this->assertSame(__DIR__ . '/Source/some_file.txt', $splFileInfo->getRealPath());

        // is relative
        $relativePath = Strings::substring($splFileInfo->getRealPath(), strlen(getcwd()) + 1);
        $this->assertSame('tests/Posts/Year2018/Exceptions/Source/some_file.txt', $relativePath);
    }

    public function testSymfonyFinder(): void
    {
        $finder = Finder::create()->files()
            ->in(__DIR__ . '/Source');

        $files = iterator_to_array($finder->getIterator());
        $this->assertCount(1, $files);

        /** @var SplFileInfo $file */
        $file = array_pop($files);

        // is relative to directory finder looked into
        // @see Symfony: https://github.com/symfony/symfony/blob/00e5cd9a1c237e579e6327e9a66c512bf76f292a/src/Symfony/Component/Finder/Iterator/RecursiveDirectoryIterator.php#L73
        $this->assertSame('some_file.txt', $file->getRelativePathname());

        // is absolute
        $this->assertSame(__DIR__ . '/Source/some_file.txt', $file->getPathname());

        // is absolute
        $this->assertSame(__DIR__ . '/Source/some_file.txt', $file->getRealPath());

        // is relative
        $relativePath = Strings::substring($file->getRealPath(), strlen(getcwd()) + 1);
        $this->assertSame('tests/Posts/Year2018/Exceptions/Source/some_file.txt', $relativePath);
    }
}
