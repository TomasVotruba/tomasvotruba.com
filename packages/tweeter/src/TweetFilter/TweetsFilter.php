<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\ValueObject\Tweet;

final class TweetsFilter
{
    public function __construct(
        private OldTweetsFilter $oldTweetsFilter,
        private PublishedTweetsFilter $publishedTweetsFilter
    ) {
    }

    /**
     * @param Tweet[] $postTweets
     * @return Tweet[]
     */
    public function filter(array $postTweets): array
    {
        $postTweets = $this->oldTweetsFilter->filter($postTweets);

        return $this->publishedTweetsFilter->filter($postTweets);
    }
}
