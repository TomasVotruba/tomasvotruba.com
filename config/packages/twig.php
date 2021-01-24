<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symplify\Amnesia\Functions\env;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'default_path' => '%kernel.project_dir%/templates',
        'paths' => [
            __DIR__ . '/../../packages/framework-stats/templates',
            __DIR__ . '/../../packages/cleaning-lady/templates',
            __DIR__ . '/../../packages/blog/templates',
            __DIR__ . '/../../packages/cleaning-lady/templates',
        ],
        'globals' => [
            'google_analytics_tracking_id' => 'UA-46082345-1',
            'site_title' => 'Tomas Votruba',
            'site_url' => env('SITE_URL'),
            'disqus_shortname' => 'itsworthsharing',
        ],
        // see https://symfony.com/blog/new-in-symfony-2-7-default-date-and-number-format-configuration
        'number_format' => [
            'decimals' => 0,
            'decimal_point' => '.',
            'thousands_separator' => ' ',
        ],
    ]);
};
