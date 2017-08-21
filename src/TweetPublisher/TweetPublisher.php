<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use TomasVotruba\Website\TweetPublisher\TwitterApi\TwitterApiWrapper;
use Tracy\Debugger;

/**
 * @inspire https://gist.github.com/petrvacha/28ec8f5eac39283f1e7dce350f5a65ad
 * @thanks Petr Vacha
 */
final class TweetPublisher
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
     * @return string[]
     */
    public function getRecentTweets(): array
    {
        $allPostTweets = $this->postTweetsProvider->provide();

        $allPublishedTweets = $this->twitterApiWrapper->getPublishedTweets();

        $tweetsToPublish = $this->excludeAlreadyPublishedTweets($allPostTweets, $allPublishedTweets);

        dump($tweetsToPublish);
        die;

//        $publishedPostTweets = $this->tweetFilter->filterOnlyPostTweets($allPublishedTweets);
        dump(array_pop($publishedPostTweets));
        die;

        // is ready? -> pick the best one...
        $this->publishTweet($tweet);
    }

    /**
     * @param string[] $allPostTweets
     * @param string[] $allPublishedTweets
     * @return string[]
     */
    private function excludeAlreadyPublishedTweets(array $allPostTweets, array $allPublishedTweets): array
    {
        Debugger::$maxLength = 1525;

        dump($allPostTweets);
        dump($allPublishedTweets);
        die;
    }
}
