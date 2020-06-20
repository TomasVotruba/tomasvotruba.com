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
    private array $publishedPostTweets = [];

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
        foreach ($this->getPublishedPostTweets() as $publishedTweet) {
            if ($postTweet->isSimilarToPublishedTweet($publishedTweet)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PublishedTweet[]
     */
    private function getPublishedPostTweets(): array
    {
        if ($this->publishedPostTweets !== []) {
            return $this->publishedPostTweets;
        }

        $this->publishedPostTweets = $this->twitterApiWrapper->getPublishedTweets();

        return $this->publishedPostTweets;
    }
}
