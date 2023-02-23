<?php

declare(strict_types=1);

use App\Providers\RouteServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
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
        CacheServiceProvider::class,
        ConsoleSupportServiceProvider::class,
        DatabaseServiceProvider::class,
        FilesystemServiceProvider::class,
        FoundationServiceProvider::class,
        ViewServiceProvider::class,
        TranslationServiceProvider::class,
        RouteServiceProvider::class,
        \Illuminate\Queue\QueueServiceProvider::class,
    ],
];
