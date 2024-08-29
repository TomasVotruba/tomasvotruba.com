<?php

declare(strict_types=1);

namespace App\Tests\Smoking;

use App\Tests\AbstractTestCase;
use Illuminate\Routing\RedirectController;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;

final class ControllerSmokeTest extends AbstractTestCase
{
    #[DataProvider('provideRoutes')]
    public function testGetRoutesRender(Route $route): void
    {
        $testResponse = $this->get($route->uri);

        $testResponse->assertSuccessful();
    }

    public static function provideRoutes(): Iterator
    {
        /** @var Route[] $getRoutes */
        $getRoutes = RouteFacade::getRoutes()->getRoutesByMethod()['GET'];

        foreach ($getRoutes as $getRoute) {
            if (self::isRedirectRoute($getRoute)) {
                continue;
            }

            if ($getRoute->parameterNames() !== []) {
                continue;
            }

            // system routes
            if (str_starts_with((string) $getRoute->uri, '_')) {
                continue;
            }

            yield [$getRoute];
        }
    }

    private static function isRedirectRoute(Route $route): bool
    {
        $controllerClass = $route->getControllerClass();
        if (! is_string($controllerClass)) {
            return false;
        }

        return ltrim($controllerClass, '\\') === RedirectController::class;
    }
}
