<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use TomasVotruba\PunchCard\CacheConfig;

return CacheConfig::make()
    ->default(env('CACHE_DRIVER', 'file'))
    ->stores([
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
        'file' => [
            'driver' => 'file',
            'path' => __DIR__ . '/../storage/framework/cache/data',
        ],
    ])
    ->prefix(env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_cache_'))
    ->toArray();
