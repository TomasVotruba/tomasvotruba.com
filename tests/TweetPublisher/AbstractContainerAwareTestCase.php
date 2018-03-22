<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\Statie\DependencyInjection\ContainerFactory;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ContainerInterface
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
