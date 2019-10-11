<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2019\FinalMock\Hook;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\Posts\Year2019\FinalMock\FinalClass;

final class FinalClassHookTest extends TestCase
{
    public function test(): void
    {
        /** @var MockObject|FinalClass $finalClassMock */
        $finalClassMock = $this->createMock(FinalClass::class);
        $finalClassMock->method('getNumber')
            ->willReturn(20);

        $this->assertSame(20, $finalClassMock->getNumber());
    }
}
