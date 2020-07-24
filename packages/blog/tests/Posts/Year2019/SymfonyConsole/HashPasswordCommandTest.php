<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyConsole;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyConsole\Command\HashPasswordCommand;

final class HashPasswordCommandTest extends TestCase
{
    public function test(): void
    {
        $application = new Application();
        // required for testing output
        $application->setAutoExit(false);
        $application->add(new HashPasswordCommand());

        // same as when you run "bin/console hash-password Y2Kheslo123"
        $stringInput = new StringInput('hash-password Y2Kheslo123');
        $bufferedOutput = new BufferedOutput();

        $result = $application->run($stringInput, $bufferedOutput);

        // 0 = success, sth else = fail
        $this->assertSame(0, $result);
        $this->assertStringStartsWith('Your hashed password is: $2y$10$', $bufferedOutput->fetch());
    }
}
