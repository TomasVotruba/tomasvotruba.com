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

    protected function setUp(): void
    {
        $containerFactory = new ContainerFactory();
        $this->container = $containerFactory->createWithConfig(__DIR__ . '/../../statie.yml');
    }
}
