<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use TomasVotruba\PunchCard\SessionConfig;

return SessionConfig::make()
    ->driver(env('SESSION_DRIVER', 'file'))
    ->lifetime(env('SESSION_LIFETIME', 120))
    ->expireOnClose(false)
    ->encrypt(false)
    ->files(storage_path('framework/sessions'))
    ->connection(env('SESSION_CONNECTION'))
    ->table('sessions')
    ->store(env('SESSION_STORE'))
    ->lottery([2, 100])
    ->cookie(env('SESSION_COOKIE', Str::slug(env('APP_NAME', 'laravel'), '_') . '_session'))
    ->path('/')
    ->domain(env('SESSION_DOMAIN'))
    ->secure(env('SESSION_SECURE_COOKIE'))
    ->httpOnly(true)
    ->sameSite('lax')
    ->toArray();
