<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/bootstrap/app.php', __DIR__ . '/config', __DIR__ . '/tests'])
    ->withSkip([
        \Rector\CodeQuality\Rector\ClassMethod\InlineArrayReturnAssignRector::class => [
            __DIR__ . '/src/Repository/ToolRepository.php',
        ],
    ])
    ->withAttributesSets()
    ->withImportNames(removeUnusedImports: true)
    ->withRules([DeclareStrictTypesRector::class])
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        naming: true,
        privatization: true,
        typeDeclarations: true,
        instanceOf: true
    )
    ->withPhpSets();
