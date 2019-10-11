<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2019\FinalMock;

use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TomasVotruba\Website\Posts\Year2019\FinalMock\FinalClass;

final class SetUpFinalClassTest extends TestCase
{
    protected function setUp(): void
    {
        BypassFinals::enable();

        $hasException = false;
        try {
            $this->createMock(FinalClass::class);
        } catch (RuntimeException $runtimeException) {
            $hasException = true;
        }

        $this->assertTrue($hasException);
    }

    public function test(): void
    {
        $this->assertFalse(false);
    }
}
