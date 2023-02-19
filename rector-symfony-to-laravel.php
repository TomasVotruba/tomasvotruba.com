<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Rector\Class_\MoveCurrentDateTimeDefaultInEntityToConstructorRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->importNames();

    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/packages']);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Symfony\Bundle\FrameworkBundle\Controller\AbstractController' => 'Illuminate\Routing\Controller',
    ]);

    // in the making :)
    $rectorConfig->ruleWithConfiguration(MethodCallToFuncCallRector::class);
};
