<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use TomasVotruba\Website\ValueObject\Symfony\Extension\RoutingExtension;

// @see https://symfony.com/doc/current/routing.html#route-groups-and-prefixes

return static function (RoutingConfigurator $routes): void {
    $routes->import(__DIR__ . '/../src/Controller', RoutingExtension::TYPE_ANNOTATION);
    $routes->import(__DIR__ . '/../packages/blog/src/Controller', RoutingExtension::TYPE_ANNOTATION);
    $routes->import(__DIR__ . '/../packages/framework-stats/src/Controller', RoutingExtension::TYPE_ANNOTATION);
    $routes->import(__DIR__ . '/../packages/projects/src/Controller', RoutingExtension::TYPE_ANNOTATION);
};
