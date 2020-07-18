<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use Symplify\SmartFileSystem\FileSystemGuard;
use TomasVotruba\Tweeter\TwitterApi\TwitterApiFactory;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('twitter_name', '%env(TWEETER_NAME)%');

    $parameters->set('twitter_minimal_gap_in_days', 1);

    $parameters->set('twitter_maximal_days_in_past', 300);

    $parameters->set('twitter_consumer_key', '%env(TWITTER_CONSUMER_KEY)%');

    $parameters->set('twitter_consumer_secret', '%env(TWITTER_CONSUMER_SECRET)%');

    $parameters->set('twitter_oauth_access_token', '%env(TWITTER_OAUTH_ACCESS_TOKEN)%');

    $parameters->set('twitter_oauth_access_token_secret', '%env(TWITTER_OAUTH_ACCESS_TOKEN_SECRET)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('TomasVotruba\Tweeter\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception/*', __DIR__ . '/../src/ValueObject/*']);

    $services->set(TwitterAPIExchange::class, TwitterAPIExchange::class)
        ->factory([ref(TwitterApiFactory::class), 'create']);

    $services->set(FileSystemGuard::class);
};
