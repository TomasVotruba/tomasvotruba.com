<?php

declare(strict_types=1);

use TomasVotruba\PunchCard\ViewConfig;

return ViewConfig::make()
    ->paths([__DIR__ . '/../resources/views'])
    ->compiled(__DIR__ . '/../storage/framework/views')
    ->toArray();
