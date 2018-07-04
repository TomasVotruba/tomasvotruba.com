<?php declare(strict_types=1);

namespace TomasVotruba\StatieTweetPublisher\TwitterApi;

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
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->oauthAccessToken = $oauthAccessToken;
        $this->oauthAccessTokenSecret = $oauthAccessTokenSecret;
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
