<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\TwitterApi\TwitterApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;
use TomasVotruba\Tweeter\ValueObject\Tweet;

final class PublishedTweetsFilter
{
    private TwitterApiWrapper $twitterApiWrapper;
    /**
     * @var PublishedTweet[]
     */
    private array $publishedTweets = [];

    public function __construct(TwitterApiWrapper $twitterApiWrapper)
    {
        $this->twitterApiWrapper = $twitterApiWrapper;
    }

    /**
     * @param Tweet[] $allTweets
     * @return Tweet[]
     */
    public function filter(array $allTweets): array
    {
        return array_filter($allTweets, fn ($tweet): bool => ! $this->wasTweetPublished($tweet));
    }

    private function wasTweetPublished(Tweet $postTweet): bool
    {
        foreach ($this->getPublishedTweets() as $publishedTweet) {
            if ($postTweet->isSimilarToPublishedTweet($publishedTweet)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PublishedTweet[]
     */
    private function getPublishedTweets(): array
    {
        if ($this->publishedTweets !== []) {
            return $this->publishedTweets;
        }

        return $this->publishedTweets = $this->twitterApiWrapper->getPublishedTweets();
    }
}
