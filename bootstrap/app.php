<?php

declare(strict_types=1);

use App\PHPStanRules\Command\ScanPHPStanRulesCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

$applicationBuilder = Application::configure()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->withCommands([
        ScanPHPStanRulesCommand::class,
    ])
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('app:scan-phpstan-rules')->daily();
    })
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->create();

$applicationBuilder->useAppPath(__DIR__ . '/../src');

return $applicationBuilder;
