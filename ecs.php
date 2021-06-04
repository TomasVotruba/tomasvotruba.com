<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/config',
        __DIR__ . '/packages',
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
    ]);

    $parameters->set(Option::SKIP, [__DIR__ . '/config/bundles.php', UnaryOperatorSpacesFixer::class]);

    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::COMMON);
    $containerConfigurator->import(SetList::SYMPLIFY);

    $services = $containerConfigurator->services();
    $services->set(StandaloneLineInMultilineArrayFixer::class);
    $services->set(LineLengthFixer::class);
};
