<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel;

final class ConsoleKernel extends Kernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:tweet-post')->weekdays()->at('08:00')->timezone('Europe/Paris');
    }
}
