<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;

$applicaiton = new Application(__DIR__ . '/..');

$applicaiton->singleton(Kernel::class,App\Http\HttpKernel::class);

$applicaiton->singleton(Illuminate\Contracts\Console\Kernel::class,\Illuminate\Foundation\Console\Kernel::class);

$applicaiton->singleton(ExceptionHandler::class, Handler::class);

return $applicaiton;
