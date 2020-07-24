<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\SymfonyFinder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class SymfonySplFileInfoTest extends TestCase implements PostTestInterface
{
    public function testRelatives(): void
    {
        $iterator = Finder::create()
            ->files()
            ->in(__DIR__)
            ->name('composer.json')
            ->getIterator();

        foreach ($iterator as $splFileInfo) {
            $this->assertSame('Source/composer.json', $splFileInfo->getRelativePathname());
            $this->assertSame('Source', $splFileInfo->getRelativePath());
        }
    }

    public function getPostId(): int
    {
        return 131;
    }
}
