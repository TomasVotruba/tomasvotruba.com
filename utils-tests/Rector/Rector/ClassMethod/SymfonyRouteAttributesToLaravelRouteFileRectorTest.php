<?php

declare(strict_types=1);

namespace TomasVotruba\Utils\Tests\Rector\Rector\ClassMethod;

use Nette\Utils\FileSystem;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class SymfonyRouteAttributesToLaravelRouteFileRectorTest extends AbstractRectorTestCase
{
    protected function tearDown(): void
    {
        // clear routes
        FileSystem::delete(__DIR__ . '/config/dumped_routes.php');
    }

    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixture/some_controller.php.inc');

        $this->assertFileWasAdded(
            __DIR__ . '/config/dumped_routes.php',
            FileSystem::read(__DIR__ . '/Expected/expected_dumped_routes.php')
        );
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
