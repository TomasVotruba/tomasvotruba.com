<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Strings\StringFormatConverter;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('TomasVotruba\Blog\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/ValueObject', __DIR__ . '/../src/Exception', __DIR__ . '/../src/Posts']);

    $services->set(FinderSanitizer::class);

    $services->set(StringFormatConverter::class);

    $services->set(ParsedownExtra::class, ParsedownExtra::class);

    $services->load('TomasVotruba\Blog\Tests\\', __DIR__ . '/../tests')
        ->autowire(false);
};
