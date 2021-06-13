<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\MethodCall\UseMessageVariableForSprintfInSymfonyStyleRector;
use Rector\Core\Configuration\Option;
use Rector\Doctrine\Rector\Class_\MoveCurrentDateTimeDefaultInEntityToConstructorRector;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Nette\Set\NetteSetList;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $containerConfigurator->import(SetList::PHP_74);
    $containerConfigurator->import(SetList::PHP_80);
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::CODING_STYLE);
    $containerConfigurator->import(SetList::NAMING);
    $containerConfigurator->import(SetList::TYPE_DECLARATION);
    $containerConfigurator->import(SetList::TYPE_DECLARATION_STRICT);
    $containerConfigurator->import(NetteSetList::NETTE_UTILS_CODE_QUALITY);
    $containerConfigurator->import(DoctrineSetList::DOCTRINE_CODE_QUALITY);

    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/packages']);

    $parameters->set(Option::SKIP, [
        RemoveExtraParametersRector::class,
        UseMessageVariableForSprintfInSymfonyStyleRector::class,
        MoveCurrentDateTimeDefaultInEntityToConstructorRector::class,

        // broken for DateTime interface
        RenamePropertyToMatchTypeRector::class => [
            __DIR__  . '/packages/blog/src/ValueObject/Post.php',
            __DIR__ . '/packages/tweeter/src/ValueObject/PublishedTweet.php',
        ],
    ]);
};
