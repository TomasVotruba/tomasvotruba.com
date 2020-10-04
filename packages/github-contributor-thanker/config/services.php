<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Http\BetterGuzzleClient;
use Symplify\SmartFileSystem\SmartFileSystem;
use TomasVotruba\GithubContributorsThanker\ValueObject\Option;
use TomasVotruba\Website\ValueObject\Option as OptionAlias;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(OptionAlias::THANKER_REPOSITORY_NAME, 'tomasvotruba/tomasvotruba.com');
    $parameters->set(Option::THANKER_AUTHOR_NAME, 'TomasVotruba');
    $parameters->set(OptionAlias::GITHUB_TOKEN, '%env(GITHUB_TOKEN)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->load('TomasVotruba\GithubContributorsThanker\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception']);

    $services->set(SmartFileSystem::class);
    $services->set(BetterGuzzleClient::class);
};
