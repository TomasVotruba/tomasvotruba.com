<?php

declare(strict_types=1);

// allows using facades in data providers
// @see https://stackoverflow.com/a/26774924/1348344

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

require __DIR__ . '/../vendor/autoload.php';

function createApplication(): Application
{
    $application = require __DIR__ . '/../bootstrap/app.php';

    /** @var Kernel $kernel */
    $kernel = $application->make(Kernel::class);
    $kernel->bootstrap();

    return $application;
}

createApplication();
