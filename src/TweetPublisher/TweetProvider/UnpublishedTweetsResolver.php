<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\TweetProvider;

use Nette\Utils\Strings;
use TomasVotruba\Website\TweetPublisher\Tweet\Tweet;

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
                if (Strings::startsWith(
                    $tweet->getText(),
                    // published tweet is usually modified by Twitter API, so we just use starting part of it
                    substr($publishedTweet->getText(), 0, 50)
                )) {
                    continue 2;
                }
            }

            $unpublishedTweets[] = $tweet;
        }

        return $unpublishedTweets;
    }
}
