<?php

declare(strict_types=1);

// allows using facades in data providers
// @see https://stackoverflow.com/a/26774924/1348344

namespace App\DependencyInjection;

use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;

final class ContainerFactory
{
    public function create(): Container
    {
        $application = require __DIR__ . '/../../bootstrap/app.php';

        /** @var Kernel $kernel */
        $kernel = $application->make(Kernel::class);
        $kernel->bootstrap();

        return $application;
    }
}
