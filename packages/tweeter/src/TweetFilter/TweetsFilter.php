<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\ValueObject\PostTweet;

final class TweetsFilter
{
    public function __construct(
        private readonly OldTweetsFilter $oldTweetsFilter,
        private readonly PublishedTweetsFilter $publishedTweetsFilter
    ) {
    }

    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        $postTweets = $this->oldTweetsFilter->filter($postTweets);
        return $this->publishedTweetsFilter->filter($postTweets);
    }
}
