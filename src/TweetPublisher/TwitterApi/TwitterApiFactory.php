<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\TwitterApi;

use TwitterAPIExchange;

/**
 * This factory only allows to use ENV variable without any config dependency
 */
final class TwitterApiFactory
{
    /**
     * @var string
     */
    private $consumerKey;

    /**
     * @var string
     */
    private $consumerSecret;

    /**
     * @var string
     */
    private $oauthAccessToken;

    /**
     * @var string
     */
    private $oauthAccessTokenSecret;

    public function __construct(
        string $consumerKey,
        string $consumerSecret,
        string $oauthAccessToken,
        string $oauthAccessTokenSecret
    ) {
        $this->consumerKey = getenv('TWITTER_CONSUMER_KEY') ?: $consumerKey;
        $this->consumerSecret = getenv('TWITTER_CONSUMER_SECRET')?: $consumerSecret;
        $this->oauthAccessToken = getenv('TWITTER_OAUTH_ACCESS_TOKEN')?: $oauthAccessToken;
        $this->oauthAccessTokenSecret = getenv('TWITTER_ACCESS_TOKEN_SECRET')?: $oauthAccessTokenSecret;
    }

    public function create(): TwitterAPIExchange
    {
        return new TwitterAPIExchange([
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'oauth_access_token' => $this->oauthAccessToken,
            'oauth_access_token_secret' => $this->oauthAccessTokenSecret,
        ]);
    }
}
