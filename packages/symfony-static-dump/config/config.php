<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('TomasVotruba\SymfonyStaticDump\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Contract/*']);
};
