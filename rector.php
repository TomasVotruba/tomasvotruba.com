<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app', __DIR__ . '/config', __DIR__ . '/tests'
    ])
    ->withImportNames(removeUnusedImports: true)
    ->withRules([DeclareStrictTypesRector::class])
    ->withPreparedSets(codeQuality: true, codingStyle: true, naming: true, privatization: true, typeDeclarations: true, instanceOf: true)
    ->withPhpSets();
