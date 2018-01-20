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
                // this comparison is needed, because urls are already appended to $publishedTweet
                if (Strings::startsWith($publishedTweet->getText(), $tweet->getText())) {
                    continue 2;
                }
            }

            $unpublishedTweets[] = $tweet;
        }

        return $unpublishedTweets;
    }
}
