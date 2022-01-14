<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Website\ValueObject\Option;
use TwitterAPIExchange;

/**
 * @api
 * This factory only allows to use ENV variable without any config dependency
 */
final class TwitterApiFactory
{
    private readonly string $twitterConsumerKey;

    private readonly string $twitterConsumerSecret;

    private readonly string $twitterOauthAccessToken;

    private readonly string $twitterOauthAccessTokenSecret;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->twitterConsumerKey = $parameterProvider->provideStringParameter(Option::TWITTER_CONSUMER_KEY);
        $this->twitterConsumerSecret = $parameterProvider->provideStringParameter(Option::TWITTER_CONSUMER_SECRET);
        $this->twitterOauthAccessToken = $parameterProvider->provideStringParameter(
            Option::TWITTER_OAUTH_ACCESS_TOKEN
        );
        $this->twitterOauthAccessTokenSecret = $parameterProvider->provideStringParameter(
            Option::TWITTER_OAUTH_ACCESS_TOKEN_SECRET
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
