<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\ValueObject\Tweet;

final class TweetsFilter
{
    private OldTweetsFilter $oldTweetsFilter;

    private PublishedTweetsFilter $publishedTweetsFilter;

    public function __construct(OldTweetsFilter $oldTweetsFilter, PublishedTweetsFilter $publishedTweetsFilter)
    {
        $this->oldTweetsFilter = $oldTweetsFilter;
        $this->publishedTweetsFilter = $publishedTweetsFilter;
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
