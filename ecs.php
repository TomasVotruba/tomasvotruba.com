<?php

declare(strict_types=1);

use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/config', __DIR__ . '/app', __DIR__ . '/tests'])
    ->withRootFiles()
    ->withPreparedSets(psr12: true, common: true)
    ->withRules([LineLengthFixer::class]);
