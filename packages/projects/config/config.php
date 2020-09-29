<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Spatie\Packagist\PackagistClient;
use Spatie\Packagist\PackagistUrlGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('TomasVotruba\Projects\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/ValueObject']);

    // packagist
    $services->set(Client::class);
    $services->set(PackagistUrlGenerator::class);
    $services->set(PackagistClient::class);
};
