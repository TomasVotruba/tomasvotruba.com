<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symplify\Amnesia\Functions\env;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use TomasVotruba\Website\ValueObject\Option;

return function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/../packages/*/config/*.php');
    $containerConfigurator->import(__DIR__ . '/packages/*');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SITE_URL, env('SITE_URL'));

    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->load('TomasVotruba\Website\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject', __DIR__ . '/../src/Exception']);

    $services->set(PrivatesAccessor::class);
};
