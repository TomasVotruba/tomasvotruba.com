<?php declare(strict_types=1);

namespace TomasVotruba\Website\Posts\Year2018\ProcessAsync;

use Symfony\Component\Process\Process;

final class ProcessesInParallel
{
    /**
     * @var int[]
     */
    private $sleepIntervalsInMs = [];

    /**
     * @var Process[]
     */
    private $activeProcesses = [];

    /**
     * @param mixed[] $sleepIntervalsInMs
     */
    public function __construct(array $sleepIntervalsInMs)
    {
        $this->sleepIntervalsInMs = $sleepIntervalsInMs;
    }

    public function run(): void
    {
        // 1. start them all
        $this->startProcesses();

        // 2. wait until they're finished
        $this->waitUntilProcessesAreFinished();
    }

    private function startProcesses(): void
    {
        foreach ($this->sleepIntervalsInMs as $sleepInMs) {
            $process = new Process('sleep ' . ($sleepInMs / 1000));
            $process->start();

            $this->activeProcesses[] = $process;
        }
    }

    private function waitUntilProcessesAreFinished(): void
    {
        while (count($this->activeProcesses)) {
            foreach ($this->activeProcesses as $i => $runningProcess) {
                // specific process is finished, so we remove it
                if (! $runningProcess->isRunning()) {
                    unset($this->activeProcesses[$i]);
                }

                // check every 100 ms
                usleep(10);
            }
        }
    }
}
