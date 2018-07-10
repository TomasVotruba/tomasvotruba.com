<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\LatteTwig;

use PHPUnit\Framework\TestCase;
use Symplify\LatteToTwigConverter\DependencyInjection\ContainerFactory;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;

final class LatteToTwigConverterTest extends TestCase
{
    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->create();

        $this->latteToTwigConverter = $container->get(LatteToTwigConverter::class);
    }

    public function test(): void
    {
        $convertedContent = $this->latteToTwigConverter->convertFile(__DIR__ . '/Source/latte-file.latte');

        $this->assertStringEqualsFile(__DIR__ . '/Source/expected-twig-file.twig', $convertedContent);
    }
}
