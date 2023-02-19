<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/packages',
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/utils',
        __DIR__ . '/utils-tests',
    ]);

    $ecsConfig->import(SetList::SYMPLIFY);

    $ecsConfig->skip(['*/Fixture/*', '*/Expected/*', UnaryOperatorSpacesFixer::class]);

    $ecsConfig->sets([SetList::PSR_12, SetList::COMMON, SetList::SYMPLIFY]);

    $ecsConfig->rule(LineLengthFixer::class);
};
