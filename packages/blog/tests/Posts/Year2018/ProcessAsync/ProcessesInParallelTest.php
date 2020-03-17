<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\ProcessAsync;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Stopwatch\Stopwatch;
use TomasVotruba\Blog\Posts\Year2018\ProcessAsync\ProcessesInParallel;

final class ProcessesInParallelTest extends TestCase
{
    /**
     * @var int[]
     */
    private const SLEEP_INTERVALS_IN_MS = [100, 200, 300];
    private ProcessesInParallel $processesInParallel;

    private Stopwatch $stopwatch;

    protected function setUp(): void
    {
        $this->stopwatch = new Stopwatch();
        $this->processesInParallel = new ProcessesInParallel(self::SLEEP_INTERVALS_IN_MS);
    }

    public function test(): void
    {
        $this->stopwatch->start('test');

        $this->processesInParallel->run();

        $stopwatchEvent = $this->stopwatch->stop('test');

        $maxDurationInMs = array_sum(self::SLEEP_INTERVALS_IN_MS);
        $realDurationInMs = $stopwatchEvent->getDuration();

        $this->assertLessThan($maxDurationInMs, $realDurationInMs);
    }
}
