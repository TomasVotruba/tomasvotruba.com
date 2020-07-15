<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use ParsedownExtra;
use Symplify\PackageBuilder\Strings\StringFormatConverter;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
        ->bind('$projectDir', '%kernel.project_dir%');

    $services->load('TomasVotruba\Blog\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/ValueObject/*',
            __DIR__ . '/../src/Exception/*',
            __DIR__ . '/../src/Posts/*',
        ]);

    $services->set(FinderSanitizer::class);

    $services->set(StringFormatConverter::class);

    $services->set(ParsedownExtra::class, ParsedownExtra::class);

    $services->load('TomasVotruba\Blog\Tests\\', __DIR__ . '/../tests')
        ->exclude(__DIR__ . '/../tests/Posts/Year2019/SymfonyEventDispatcher/Source/Event/*');
};
