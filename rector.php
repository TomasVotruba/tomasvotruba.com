<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Rector\Class_\MoveCurrentDateTimeDefaultInEntityToConstructorRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->importNames();

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::NAMING,
        SetList::TYPE_DECLARATION,
        LevelSetList::UP_TO_PHP_81,
    ]);


    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/packages']);

    $rectorConfig->skip([
        RemoveExtraParametersRector::class,
        MoveCurrentDateTimeDefaultInEntityToConstructorRector::class,

        // broken for DateTime interface
        RenamePropertyToMatchTypeRector::class => [
            __DIR__  . '/packages/blog/src/ValueObject/Post.php',
        ],
    ]);
};
