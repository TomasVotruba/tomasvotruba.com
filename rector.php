<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->importNames();

    $rectorConfig->rules([
        DeclareStrictTypesRector::class,
    ]);

    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_100,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::INSTANCEOF,
        LevelSetList::UP_TO_PHP_81,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/tests'
    ]);
};
