<?php declare(strict_types=1);

namespace TomasVotruba\Website\Tests\Posts\Year2018\ProcessAsync;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Stopwatch\Stopwatch;
use TomasVotruba\Website\Posts\Year2018\ProcessAsync\ProcessesInParallel;

final class ProcessesInParallelTest extends TestCase
{
    /**
     * @var int[]
     */
    private $sleepIntervalsInMs = [100, 200, 300];

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @var ProcessesInParallel
     */
    private $processesInParallel;

    protected function setUp(): void
    {
        $this->stopwatch = new Stopwatch();
        $this->processesInParallel = new ProcessesInParallel($this->sleepIntervalsInMs);
    }

    public function test(): void
    {
        $this->stopwatch->start('test');

        $this->processesInParallel->run();

        $stopwatchEvent = $this->stopwatch->stop('test');

        $maxDurationInMs = array_sum($this->sleepIntervalsInMs);
        $realDurationInMs = $stopwatchEvent->getDuration();

        $this->assertLessThan($maxDurationInMs, $realDurationInMs);
    }
}
