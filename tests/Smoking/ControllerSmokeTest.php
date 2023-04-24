<?php

declare(strict_types=1);

namespace App\Tests\Smoking;

use App\Tests\AbstractTestCase;
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
        $getRoutes = RouteFacade::getRoutes()->getRoutesByMethod()['GET'];

        foreach ($getRoutes as $getRoute) {
            if ($getRoute->parameterNames() !== []) {
                continue;
            }

            // system routes
            if (str_starts_with($getRoute->uri, '_')) {
                continue;
            }

            yield [$getRoute];
        }
    }
}
