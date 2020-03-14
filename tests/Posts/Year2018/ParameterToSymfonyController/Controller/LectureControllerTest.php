<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\ParameterToSymfonyController\Controller;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use TomasVotruba\Website\Posts\Year2018\ParameterToSymfonyController\App\ParameterToSymfonyControllerAppKernel;
use TomasVotruba\Website\Posts\Year2018\ParameterToSymfonyController\Controller\LectureController;

final class LectureControllerTest extends AbstractKernelTestCase
{
    private LectureController $lectureController;

    protected function setUp(): void
    {
        $this->bootKernel(ParameterToSymfonyControllerAppKernel::class);

        $this->lectureController = self::$container->get(LectureController::class);
    }

    public function testParameterIsInjectedViaConstructor(): void
    {
        $this->assertSame('12345', $this->lectureController->getBankAccount());
    }
}
