<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Console;

use Symfony\Component\Console\Application;
use TomasVotruba\FrameworkStats\Console\Command\GenerateStatsCommand;

final class FrameworkStatsApplication extends Application
{
    public function __construct(GenerateStatsCommand $generateStatsCommand)
    {
        $this->add($generateStatsCommand);

        parent::__construct();
    }
}
