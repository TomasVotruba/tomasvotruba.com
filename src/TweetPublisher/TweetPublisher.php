<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use TomasVotruba\Website\TweetPublisher\TwitterApi\TwitterApiWrapper;

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

    public function run(): void
    {
        $allPostTweets = $this->postTweetsProvider->provide();
        $allPublishedTweets = $this->twitterApiWrapper->getPublishedTweets();

        $tweetsToPublish = $this->excludeAlreadyPublishedTweets($allPostTweets, $allPublishedTweets);

        if (! count($tweetsToPublish)) {
            return;
        }

        dump($tweetsToPublish);
        // get date of last tweet, is it allowed to publsieh new one?
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
        return array_diff_assoc($allPostTweets, $allPublishedTweets);
    }
}
