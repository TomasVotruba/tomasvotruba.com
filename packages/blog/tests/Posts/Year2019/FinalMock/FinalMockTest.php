<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\FinalMock;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Blog\Posts\Year2019\FinalMock\FinalClass;

final class FinalMockTest extends TestCase
{
    public function test(): void
    {
        /** @var MockObject&FinalClass $finalClassMock */
        $finalClassMock = $this->createMock(FinalClass::class);
        $finalClassMock->method('getNumber')
            ->willReturn(20);

        $this->assertSame(20, $finalClassMock->getNumber());
    }
}
