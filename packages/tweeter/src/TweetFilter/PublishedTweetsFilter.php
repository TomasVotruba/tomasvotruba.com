<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\TwitterApi\TwitterPostApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;
use TomasVotruba\Tweeter\ValueObject\Tweet;

final class PublishedTweetsFilter
{
    /**
     * @var PublishedTweet[]
     */
    private array $publishedPostTweets = [];

    public function __construct(
        private TwitterPostApiWrapper $twitterPostApiWrapper
    ) {
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

        $this->publishedPostTweets = $this->twitterPostApiWrapper->getPublishedTweets();

        return $this->publishedPostTweets;
    }
}
