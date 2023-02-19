<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Rector\Class_\MoveCurrentDateTimeDefaultInEntityToConstructorRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Transform\Rector\MethodCall\MethodCallToFuncCallRector;
use Rector\Transform\ValueObject\MethodCallToFuncCall;
use TomasVotruba\Utils\Rector\Rector\ClassMethod\SymfonyRouteAttributesToLaravelRouteFileRector;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/utils/config/rector_services.php');

    $rectorConfig->importNames();

    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/packages']);

    $rectorConfig->ruleWithConfiguration(SymfonyRouteAttributesToLaravelRouteFileRector::class, [
        SymfonyRouteAttributesToLaravelRouteFileRector::ROUTES_FILE_PATH => getcwd() . '/routes/web.php',
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Symfony\Bundle\FrameworkBundle\Controller\AbstractController' => 'Illuminate\Routing\Controller',
        'Symfony\Component\HttpFoundation\Response' => 'Illuminate\View\View',
    ]);

    // in the making :)
    $rectorConfig->ruleWithConfiguration(MethodCallToFuncCallRector::class, [
        // render() to view
        new MethodCallToFuncCall('Symfony\Bundle\FrameworkBundle\Controller\AbstractController', 'render', 'view'),
    ]);
};
