<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetProvider;

use TomasVotruba\Tweeter\TwitterApi\TwitterPostApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;

final class PublishedPostTweetsProvider
{
    /**
     * Cached published tweets
     *
     * @var PublishedTweet[]
     */
    private array $publishedPostTweets = [];

    public function __construct(
        private readonly TwitterPostApiWrapper $twitterPostApiWrapper
    ) {
    }

    /**
     * @return PublishedTweet[]
     */
    public function provide(): array
    {
        if ($this->publishedPostTweets !== []) {
            return $this->publishedPostTweets;
        }

        $this->publishedPostTweets = $this->twitterPostApiWrapper->getPublishedTweets();

        return $this->publishedPostTweets;
    }
}
