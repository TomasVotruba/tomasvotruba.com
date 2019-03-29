<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2019\FinalMock;

use PHPUnit\Framework\TestCase;
use TomasVotruba\Website\Posts\Year2019\FinalMock\FinalClass;

final class FailSetupFinalClassTest extends TestCase
{
    public function testStillFail(): void
    {
        $this->expectExceptionMessage(
            sprintf('Class "%s" is declared "final" and cannot be mocked.', FinalClass::class)
        );

        $this->createMock(FinalClass::class);
    }
}
