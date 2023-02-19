<?php

namespace TomasVotruba\Utils\Rector\DataCollector;

class RouteDataCollector
{
    /**
     * @var mixed[]
     */
    private array $collectedRouteData = [];

    public function addData(array $routeData): void
    {
        dump($routeData);
        die;
    }

    /**
     * @return array
     */
    public function getCollectedRouteData(): array
    {
        return $this->collectedRouteData;
    }


}
