<?php

declare(strict_types=1);

use TomasVotruba\PunchCard\ViewConfig;

return ViewConfig::make()
    ->paths([__DIR__ . '/../resources/views'])
    ->compiled(env('VIEW_COMPILED_PATH', realpath(storage_path('framework/views'))))
    ->toArray();
