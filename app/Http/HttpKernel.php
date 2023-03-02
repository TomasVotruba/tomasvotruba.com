<?php

declare(strict_types=1);

namespace App\Http;

use Illuminate\Foundation\Http\Kernel;

final class HttpKernel extends Kernel
{
    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [],
        'admin' => [],
    ];
}
