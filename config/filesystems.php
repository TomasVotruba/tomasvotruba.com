<?php

declare(strict_types=1);

use TomasVotruba\PunchCard\FilesystemsConfig;

return FilesystemsConfig::make()
    ->disks([
        'local' => [
            'driver' => 'local',
            'root' => storage_path(),
        ],
    ])
    ->toArray();
