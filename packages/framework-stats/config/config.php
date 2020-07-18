<?php

declare(strict_types=1);

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\SmartFileSystem\SmartFileSystem;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/parameters.php');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('TomasVotruba\FrameworkStats\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel/*', __DIR__ . '/../src/ValueObject/*']);

    $services->set(SymfonyStyleFactory::class);

    $services->set(SymfonyStyle::class)
        ->factory([ref(SymfonyStyleFactory::class), 'create']);

    $services->set(SmartFileSystem::class);
};
