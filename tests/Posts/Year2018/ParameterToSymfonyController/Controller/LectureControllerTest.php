<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\ParameterToSymfonyController\Controller;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TomasVotruba\Website\Posts\Year2018\ParameterToSymfonyController\App\ParameterToSymfonyControllerAppKernel;
use TomasVotruba\Website\Posts\Year2018\ParameterToSymfonyController\Controller\LectureController;

final class LectureControllerTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function setUp(): void
    {
        $kernel = new ParameterToSymfonyControllerAppKernel();
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    public function testParameterIsInjectedViaConstructor(): void
    {
        /** @var LectureController $lectureController */
        $lectureController = $this->container->get(LectureController::class);
        $this->assertSame('12345', $lectureController->getBankAccount());
    }
}
