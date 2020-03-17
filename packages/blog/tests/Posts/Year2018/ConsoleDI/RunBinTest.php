<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\ConsoleDI;

use PHPUnit\Framework\TestCase;

final class RunBinTest extends TestCase
{
    /**
     * @var string
     */
    private const BIN_FILE_PATH = __DIR__ . '/../../../../src/Posts/Year2018/ConsoleDI/bin/some-app';

    public function testBareBinFile(): void
    {
        $outputString = $this->execAndGetOutput(self::BIN_FILE_PATH);

        // success
        $this->assertStringContainsString('Lists commands', $outputString);
    }

    public function testSomeCommand(): void
    {
        $output = $this->execAndGetOutput(self::BIN_FILE_PATH . ' some');

        // success
        $this->assertStringContainsString('OK', $output);
    }

    private function execAndGetOutput(string $binCommand): string
    {
        exec('php ' . $binCommand, $output);

        return implode(PHP_EOL, $output);
    }
}
