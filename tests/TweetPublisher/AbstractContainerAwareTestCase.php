<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symplify\Statie\DependencyInjection\ContainerFactory;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Container
     */
    private static $cachedContainer;

    protected function setUp(): void
    {
        if (! self::$cachedContainer) {
            $containerFactory = new ContainerFactory();
            self::$cachedContainer = $containerFactory->createWithConfig(__DIR__ . '/../../statie.yml');
        }

        $this->container = self::$cachedContainer;
    }
}
