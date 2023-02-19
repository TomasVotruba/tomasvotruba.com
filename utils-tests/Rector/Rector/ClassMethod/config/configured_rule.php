<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use TomasVotruba\Utils\Rector\Rector\ClassMethod\SymfonyRouteAttributesToLaravelRouteFileRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->removeUnusedImports();

    $rectorConfig->import(__DIR__ . '/../../../../../utils/config/rector_services.php');

    $rectorConfig->ruleWithConfiguration(SymfonyRouteAttributesToLaravelRouteFileRector::class, [
        SymfonyRouteAttributesToLaravelRouteFileRector::ROUTES_FILE_PATH => __DIR__ . '/dumped_routes.php',
    ]);
};
