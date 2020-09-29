<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Spatie\Packagist\PackagistClient;
use Spatie\Packagist\PackagistUrlGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use TomasVotruba\Projects\Guzzle\CachedGuzzleFactory;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('TomasVotruba\Projects\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/ValueObject']);

    // packagist
    $services->set(Client::class)
        ->factory([ref(CachedGuzzleFactory::class), 'create']);

    $services->set(PackagistUrlGenerator::class);
    $services->set(PackagistClient::class);
};
