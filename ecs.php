<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/config',
        __DIR__ . '/packages',
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
    ]);

    $parameters->set(Option::SKIP, [
        __DIR__ . '/config/bundles.php',
        UnaryOperatorSpacesFixer::class,

        // broken on PHP 8.0
        BinaryOperatorSpacesFixer::class,
        NullableTypeDeclarationForDefaultNullValueFixer::class,
    ]);

    $parameters->set(Option::SETS, [SetList::PSR_12, SetList::COMMON, SetList::SYMPLIFY, SetList::CLEAN_CODE]);

    $services = $containerConfigurator->services();
    $services->set(StandaloneLineInMultilineArrayFixer::class);
    $services->set(LineLengthFixer::class);
};
