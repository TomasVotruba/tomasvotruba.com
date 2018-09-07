<?php declare(strict_types=1);

namespace TomasVotruba\StatieTweetPublisher\Tests;

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
     * @var ContainerInterface|null
     */
    private static $cachedContainer;

    protected function setUp(): void
    {
        if (self::$cachedContainer === null) {
            self::$cachedContainer = (new ContainerFactory())->createWithConfig(__DIR__ . '/../../../statie.yml');
        }

        $this->container = self::$cachedContainer;
    }
}
