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
        $response = $this->get($route->uri);

        $response->assertSuccessful();
    }

    public static function provideRoutes(): Iterator
    {
        $routeCollection = RouteFacade::getRoutes();

        foreach ($routeCollection->getRoutes() as $route) {
            if ($route->parameterNames() !== []) {
                continue;
            }

            // system routes
            if (str_starts_with($route->uri, '_')) {
                continue;
            }

            yield [$route];
        }
    }
}
