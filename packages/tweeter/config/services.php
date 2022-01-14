<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symplify\Amnesia\Functions\env;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\FileSystemGuard;
use TomasVotruba\Tweeter\TwitterApi\TwitterApiFactory;
use TomasVotruba\Website\ValueObject\Option;

return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::TWITTER_MINIMAL_GAP_IN_DAYS, 2);

    $parameters->set(Option::TWITTER_CONSUMER_KEY, env('TWITTER_CONSUMER_KEY'));
    $parameters->set(Option::TWITTER_CONSUMER_SECRET, env('TWITTER_CONSUMER_SECRET'));
    $parameters->set(Option::TWITTER_OAUTH_ACCESS_TOKEN, env('TWITTER_OAUTH_ACCESS_TOKEN'));
    $parameters->set(Option::TWITTER_OAUTH_ACCESS_TOKEN_SECRET, env('TWITTER_OAUTH_ACCESS_TOKEN_SECRET'));

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('TomasVotruba\Tweeter\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception', __DIR__ . '/../src/ValueObject']);

    $services->set(TwitterAPIExchange::class, TwitterAPIExchange::class)
        ->factory([service(TwitterApiFactory::class), 'create']);

    $services->set(FileSystemGuard::class);
    $services->set(ParameterProvider::class)
        ->arg('$container', service('service_container'));
};
