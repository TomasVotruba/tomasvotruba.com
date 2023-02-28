<?php

declare(strict_types=1);

use App\Providers\RouteServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use TomasVotruba\PunchCard\AppConfig;

return AppConfig::make()
    ->key(env('APP_KEY'))
    ->name(env('APP_NAME', 'TomasVotruba'))
    ->env(env('APP_ENV', 'production'))
    ->debug((bool) env('APP_DEBUG', false))
    ->url(env('APP_URL', 'http://localhost'))
    ->timezone('UTC')
    ->providers([
        // Laravel Framework Service Providers...
        CacheServiceProvider::class,
        ConsoleSupportServiceProvider::class,
        DatabaseServiceProvider::class,
        FilesystemServiceProvider::class,
        FoundationServiceProvider::class,
        ViewServiceProvider::class,
        \Illuminate\Session\SessionServiceProvider::class,
        TranslationServiceProvider::class,
        RouteServiceProvider::class,
        QueueServiceProvider::class,
    ])
    ->toArray();
