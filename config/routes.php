<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symplify\Amnesia\ValueObject\Symfony\Routing;

// @see https://symfony.com/doc/current/routing.html#route-groups-and-prefixes

return static function (RoutingConfigurator $routes): void {
    $routes->import(__DIR__ . '/../src/Controller', Routing::TYPE_ANNOTATION);
    $routes->import(__DIR__ . '/../packages/blog/src/Controller', Routing::TYPE_ANNOTATION);
    $routes->import(__DIR__ . '/../packages/cleaning-lady/src/Controller', Routing::TYPE_ANNOTATION);
};
