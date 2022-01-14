<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\ValueObject\PostTweet;

final class PublishedTweetsFilter
{
    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        dump($postTweets);
        die;

        return $unpublishedPostTweets;
    }
}
