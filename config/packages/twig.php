<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symplify\Amnesia\Functions\env;
use Symplify\Amnesia\ValueObject\Symfony\Extension\Twig\NumberFormat;
use Symplify\Amnesia\ValueObject\Symfony\Extension\TwigExtension;

return function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(TwigExtension::NAME, [
        TwigExtension::DEFAULT_PATH => '%kernel.project_dir%/templates',
        TwigExtension::GLOBALS => [
            'google_analytics_tracking_id' => 'UA-46082345-1',
            'site_title' => 'Tomas Votruba',
            'site_url' => env('SITE_URL'),
            'disqus_shortname' => 'itsworthsharing',
        ],
        // see https://symfony.com/blog/new-in-symfony-2-7-default-date-and-number-format-configuration
        TwigExtension::NUMBER_FORMAT => [
            NumberFormat::DECIMALS => 0,
            NumberFormat::DECIMAL_POINT => '.',
            NumberFormat::THOUSANDS_SEPARATOR => ' ',
        ],
    ]);
};
