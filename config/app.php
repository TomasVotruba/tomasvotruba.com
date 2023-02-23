<?php

declare(strict_types=1);

use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\View\ViewServiceProvider;

return [
    'name' => env('APP_NAME', 'TomasVotruba'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',

    'providers' => [
        // Laravel Framework Service Providers...
        \Illuminate\Cache\CacheServiceProvider::class,
        ConsoleSupportServiceProvider::class,
        \Illuminate\Database\DatabaseServiceProvider::class,
        FilesystemServiceProvider::class,
        FoundationServiceProvider::class,
        ViewServiceProvider::class,
        \Illuminate\Translation\TranslationServiceProvider::class,
        \App\Providers\RouteServiceProvider::class,
    ],
];
