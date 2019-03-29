<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2019\FinalMock;

use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\Posts\Year2019\FinalMock\FinalClass;

final class FinalClassTest extends TestCase
{
    public function testDefault(): void
    {
        $finalClass = new FinalClass();
        $this->assertSame(10, $finalClass->getNumber());
    }

    public function testFail(): void
    {
        $this->expectExceptionMessage(
            sprintf('Class "%s" is declared "final" and cannot be mocked.', FinalClass::class)
        );

        $this->createMock(FinalClass::class);
    }

    public function testFailInside(): void
    {
        BypassFinals::enable();

        $this->expectExceptionMessage(
            sprintf('Class "%s" is declared "final" and cannot be mocked.', FinalClass::class)
        );

        $this->createMock(FinalClass::class);
    }
}
