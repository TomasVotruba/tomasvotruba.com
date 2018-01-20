<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\TweetProvider;

use TomasVotruba\Website\TweetPublisher\PostTweetsProvider;
use TomasVotruba\Website\TweetPublisher\TwitterApi\TwitterApiWrapper;

final class UnpublishedTweetsProvider
{
    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    public function __construct(PostTweetsProvider $postTweetsProvider, TwitterApiWrapper $twitterApiWrapper)
    {
        $this->postTweetsProvider = $postTweetsProvider;
        $this->twitterApiWrapper = $twitterApiWrapper;
    }

    /**
     * @return mixed[]
     */
    public function provide(): array
    {
        $allTweets = $this->postTweetsProvider->provide();
        $publishedTweets = $this->twitterApiWrapper->getPublishedTweets();

        return $this->excludePublishedTweets($allTweets, $publishedTweets);
    }

    /**
     * @param string[][] $allTweets
     * @param string[][] $publishedTweets
     * @return string[][]
     */
    private function excludePublishedTweets(array $allTweets, array $publishedTweets): array
    {
        $unpublishedTweets = [];

        foreach ($allTweets as $tweet) {
            foreach ($publishedTweets as $publishedTweet) {
                if ($tweet['text'] === $publishedTweet['text']) {
                    continue 2;
                }
            }

            $unpublishedTweets[] = $tweet;
        }

        return $unpublishedTweets;
    }
}
