<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\LatteTwig;

use PHPUnit\Framework\TestCase;
use Symplify\LatteToTwigConverter\DependencyInjection\ContainerFactory;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;

final class LatteToTwigConverterTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory())->create();

        /** @var LatteToTwigConverter $latteToTwigConverter */
        $latteToTwigConverter = $container->get(LatteToTwigConverter::class);

        $convertedContent = $latteToTwigConverter->convertFile(__DIR__ . '/Source/latte-file.latte');

        $this->assertStringEqualsFile(__DIR__ . '/Source/expected-twig-file.twig', $convertedContent);
    }
}
