<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\SymfonyFinder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

final class SymfonyFinderTest extends TestCase
{
    public function testName(): void
    {
        $finder = Finder::create()
            ->files()
            ->in(__DIR__)
            ->name('#\.(yaml|yml)$#')
            ->sortByName()
            ->getIterator();

        $this->assertCount(2, $finder);

        $fileNames = [];
        foreach ($finder as $splFileInfo) {
            $fileNames[] = $splFileInfo->getFilename();
        }

        $this->assertSame(['another_config.yaml', 'some_config.yml'], $fileNames);
    }
}
