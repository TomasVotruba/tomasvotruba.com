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
            if ($this->isTweetAmongPublished($tweet, $publishedTweets)) {
                continue;
            }

            $unpublishedTweets[] = $tweet;
        }

        return $unpublishedTweets;
    }

    /**
     * @param Tweet[] $publishedTweets
     */
    private function isTweetAmongPublished(Tweet $tweet, array $publishedTweets): bool
    {
        foreach ($publishedTweets as $publishedTweet) {
            if ($tweet->isSimilarTo($publishedTweet)) {
                return true;
            }
        }

        return false;
    }
}
