<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\MethodCall\UseMessageVariableForSprintfInSymfonyStyleRector;
use Rector\Core\Configuration\Option;
use Rector\DoctrineCodeQuality\Rector\Class_\MoveCurrentDateTimeDefaultInEntityToConstructorRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Set\ValueObject\SetList;
use Rector\SymfonyCodeQuality\Rector\Attribute\ExtractAttributeRouteNameConstantsRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ExtractAttributeRouteNameConstantsRector::class);

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $parameters->set(Option::SETS, [
        SetList::PHP_74,
        SetList::PHP_80,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::NETTE_UTILS_CODE_QUALITY,
        SetList::NAMING,
        SetList::DOCTRINE_CODE_QUALITY,
    ]);

    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/packages']);

    $parameters->set(Option::SKIP, [
        RemoveExtraParametersRector::class,
        UseMessageVariableForSprintfInSymfonyStyleRector::class,
        MoveCurrentDateTimeDefaultInEntityToConstructorRector::class,

        // broken for DateTime interface
        RenamePropertyToMatchTypeRector::class => [
            __DIR__  . '/packages/blog/src/ValueObject/Post.php',
            __DIR__ . '/packages/tweeter/src/ValueObject/Tweet.php',
            __DIR__ . '/packages/tweeter/src/ValueObject/PublishedTweet.php',
        ],

        __DIR__ . '/packages/blog/tests/Posts/Year2018/Php73/Php73Test.php',
    ]);
};
