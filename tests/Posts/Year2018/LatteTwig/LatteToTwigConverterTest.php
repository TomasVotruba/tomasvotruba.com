<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\LatteTwig;

use Symplify\LatteToTwigConverter\HttpKernel\LatteToTwigConverterKernel;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteToTwigConverterTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernel(LatteToTwigConverterKernel::class);

        /** @var LatteToTwigConverter $latteToTwigConverter */
        $latteToTwigConverter = self::$container->get(LatteToTwigConverter::class);

        $fileInfo = new SmartFileInfo(__DIR__ . '/Source/latte-file.latte');
        $convertedContent = $latteToTwigConverter->convertFile($fileInfo);

        $this->assertStringEqualsFile(__DIR__ . '/Source/expected-twig-file.twig', $convertedContent);
    }
}
