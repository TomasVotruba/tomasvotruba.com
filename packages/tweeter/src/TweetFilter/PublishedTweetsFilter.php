<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use Symfony\Component\Yaml\Yaml;
use TomasVotruba\Tweeter\ValueObject\PostTweet;

final class PublishedTweetsFilter
{
    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        $publishedTweetIds = Yaml::parseFile(__DIR__ . '/../../../../data/published_tweet_ids.yaml');

        return array_filter(
            $postTweets,
            fn (PostTweet $postTweet) => ! in_array($postTweet->getId(), $publishedTweetIds, true)
        );
    }
}
