<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\SymfonyFinder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class SymfonyFinderTest extends TestCase implements PostTestInterface
{
    public function testName(): void
    {
        $iterator = Finder::create()
            ->files()
            ->in(__DIR__)
            ->name('#\.(yaml|yml)$#')
            ->sortByName()
            ->getIterator();

        $this->assertCount(2, $iterator);

        $fileNames = [];
        foreach ($iterator as $splFileInfo) {
            $fileNames[] = $splFileInfo->getFilename();
        }

        $this->assertSame(['another_config.yaml', 'some_config.yml'], $fileNames);
    }

    public function getPostId(): int
    {
        return 131;
    }
}
