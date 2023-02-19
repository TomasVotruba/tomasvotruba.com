<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../packages/*/config/*.php');

    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->load('TomasVotruba\Website\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject', __DIR__ . '/../src/Exception']);

    $services->load('TomasVotruba\Blog\\', __DIR__ . '/../packages/blog/src')
        ->exclude([__DIR__ . '/../packages/blog/src/ValueObject']);

    $services->set(ParsedownExtra::class, ParsedownExtra::class);

    $containerConfigurator->extension('framework', [
        'secret' => '12345',
    ]);

    $containerConfigurator->extension('twig', [
        'default_path' => '%kernel.project_dir%/templates',
        'globals' => [
            'google_analytics_tracking_id' => 'UA-46082345-1',
            'site_title' => 'Tomas Votruba',
            'disqus_shortname' => 'itsworthsharing',
        ],
    ]);
};
