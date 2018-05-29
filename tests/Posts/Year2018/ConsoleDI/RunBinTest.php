<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\ConsoleDI;

use PHPUnit\Framework\TestCase;

final class RunBinTest extends TestCase
{
    /**
     * @var string
     */
    private $binFilePath = __DIR__ . '/../../../../src/Posts/Year2018/ConsoleDI/bin/some-app';

    public function testBareBinFile(): void
    {
        $outputString = $this->execAndGetOutput($this->binFilePath);

        // success
        $this->assertContains('Lists commands', $outputString);
    }

    public function testSomeCommand(): void
    {
        $output = $this->execAndGetOutput($this->binFilePath . ' some');

        // success
        $this->assertContains('OK', $output);
    }

    private function execAndGetOutput(string $binCommand): string
    {
        exec('php ' . $binCommand, $output);

        return implode(PHP_EOL, $output);
    }
}
