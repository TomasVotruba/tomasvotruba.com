<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\ProcessAsync;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Stopwatch\Stopwatch;
use TomasVotruba\Blog\Posts\Year2018\ProcessAsync\ProcessesInParallel;
use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class ProcessesInParallelTest extends TestCase implements PostTestInterface
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
        $duration = $stopwatchEvent->getDuration();

        $this->assertLessThan($maxDurationInMs, $duration);
    }

    public function getPostId(): int
    {
        return 75;
    }
}
