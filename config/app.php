<?php

declare(strict_types=1);

use App\Providers\RouteServiceProvider;

use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use TomasVotruba\PunchCard\AppConfig;

return AppConfig::make()
    ->defaults()
    ->name(env('APP_NAME', 'TomasVotruba'))
    ->providers([
        CacheServiceProvider::class,
        ConsoleSupportServiceProvider::class,
        DatabaseServiceProvider::class,
        FilesystemServiceProvider::class,
        FoundationServiceProvider::class,
        ViewServiceProvider::class,
        SessionServiceProvider::class,
        RouteServiceProvider::class,
    ])
    ->toArray();
