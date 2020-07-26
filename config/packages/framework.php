<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', ['secret' => '%env(APP_SECRET)%']);

    $containerConfigurator->extension('framework', [
        'session' => [
            #csrf_protection: true
            #http_method_override: true
            # Enables session support. Note that the session will ONLY be started if you read or write from it.
            # Remove or comment this section to explicitly disable session support.
            'handler_id' => null,
            'cookie_secure' => 'auto',
            'cookie_samesite' => 'lax',
        ],
    ]);

    $containerConfigurator->extension('framework', [
        'php_errors' => [
            #esi: true
            #fragments: true
            'log' => true,
        ],
    ]);
};
