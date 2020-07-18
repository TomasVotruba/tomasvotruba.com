<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../packages/*/config/*.php');
    $containerConfigurator->import(__DIR__ . '/packages/*.yaml');

    $containerConfigurator->import(__DIR__ . '/../vendor/symplify/symfony-static-dumper/config/config.php');

    $containerConfigurator->import(__DIR__ . '/_data/*');

    $containerConfigurator->import(__DIR__ . '/_data/generated/*');

    $parameters = $containerConfigurator->parameters();

    $parameters->set('site_url', '%env(SITE_URL)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public()
        ->bind('$projectDir', '%kernel.project_dir%');

    $services->load('TomasVotruba\Website\\', __DIR__ . '/../src/*')
        ->exclude([__DIR__ . '/../src/HttpKernel/*']);
};
