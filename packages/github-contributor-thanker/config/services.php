<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\PackageBuilder\Http\BetterGuzzleClient;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('thanker_repository_name', 'tomasvotruba/tomasvotruba.com');

    $parameters->set('thanker_author_name', 'TomasVotruba');

    $parameters->set('github_token', '%env(GITHUB_TOKEN)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->load('TomasVotruba\GithubContributorsThanker\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception/*']);

    $services->set(Filesystem::class);

    $services->set(BetterGuzzleClient::class);

    $services->set(Client::class);
};
