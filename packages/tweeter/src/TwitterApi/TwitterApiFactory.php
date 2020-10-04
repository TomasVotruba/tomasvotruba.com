<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Website\ValueObject\Option as OptionAlias;
use TwitterAPIExchange;

/**
 * This factory only allows to use ENV variable without any config dependency
 */
final class TwitterApiFactory
{
    private string $twitterConsumerKey;

    private string $twitterConsumerSecret;

    private string $twitterOauthAccessToken;

    private string $twitterOauthAccessTokenSecret;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->twitterConsumerKey = $parameterProvider->provideStringParameter(OptionAlias::TWITTER_CONSUMER_KEY);
        $this->twitterConsumerSecret = $parameterProvider->provideStringParameter(OptionAlias::TWITTER_CONSUMER_SECRET);
        $this->twitterOauthAccessToken = $parameterProvider->provideStringParameter(
            OptionAlias::TWITTER_OAUTH_ACCESS_TOKEN
        );
        $this->twitterOauthAccessTokenSecret = $parameterProvider->provideStringParameter(
            OptionAlias::TWITTER_OAUTH_ACCESS_TOKEN_SECRET
        );
    }

    public function create(): TwitterAPIExchange
    {
        return new TwitterAPIExchange([
            'consumer_key' => $this->twitterConsumerKey,
            'consumer_secret' => $this->twitterConsumerSecret,
            'oauth_access_token' => $this->twitterOauthAccessToken,
            'oauth_access_token_secret' => $this->twitterOauthAccessTokenSecret,
        ]);
    }
}
