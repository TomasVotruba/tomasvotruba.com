<?php

declare(strict_types=1);

use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
    ]);

    $ecsConfig->skip(['*/Fixture/*', '*/Expected/*']);

    $ecsConfig->sets([SetList::PSR_12, SetList::COMMON, SetList::SYMPLIFY]);

    $ecsConfig->rule(LineLengthFixer::class);
};
