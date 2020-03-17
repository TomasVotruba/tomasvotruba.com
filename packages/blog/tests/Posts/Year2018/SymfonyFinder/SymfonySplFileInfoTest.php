<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\SymfonyFinder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

final class SymfonySplFileInfoTest extends TestCase
{
    public function testRelatives(): void
    {
        $finder = Finder::create()
            ->files()
            ->in(__DIR__)
            ->name('composer.json')
            ->getIterator();

        foreach ($finder as $splFileInfo) {
            $this->assertSame('Source/composer.json', $splFileInfo->getRelativePathname());
            $this->assertSame('Source', $splFileInfo->getRelativePath());
        }
    }
}
