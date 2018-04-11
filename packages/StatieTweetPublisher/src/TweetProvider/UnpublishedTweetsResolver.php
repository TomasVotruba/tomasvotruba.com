<?php declare(strict_types=1);

namespace TomasVotruba\StatieTweetPublisher\TweetProvider;

use TomasVotruba\StatieTweetPublisher\Tweet\Tweet;

final class UnpublishedTweetsResolver
{
    /**
     * @param Tweet[] $allTweets
     * @param Tweet[] $publishedTweets
     * @return Tweet[]
     */
    public function excludePublishedTweets(array $allTweets, array $publishedTweets): array
    {
        $unpublishedTweets = [];

        foreach ($allTweets as $tweet) {
            foreach ($publishedTweets as $publishedTweet) {
                if ($tweet->isSimilarTo($publishedTweet)) {
                    continue 2;
                }
            }

            $unpublishedTweets[] = $tweet;
        }

        return $unpublishedTweets;
    }
}
