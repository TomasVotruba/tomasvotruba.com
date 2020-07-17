<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Configuration\Option;

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

    $parameters->set(Option::SETS, ['psr12', 'php70', 'php71', 'common', 'symplify', 'clean-code']);

    $parameters->set(Option::SKIP, [
        UnaryOperatorSpacesFixer::class => null,
    ]);

    $services = $containerConfigurator->services();

    $services->set(StandaloneLineInMultilineArrayFixer::class);

    $services->set(LineLengthFixer::class);
};
