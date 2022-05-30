<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

// @see https://symfony.com/doc/current/routing.html#route-groups-and-prefixes

return static function (RoutingConfigurator $routes): void {
    $routes->import(__DIR__ . '/../src/Controller', 'annotation');
};
