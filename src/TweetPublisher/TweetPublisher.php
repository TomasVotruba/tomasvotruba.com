<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use TomasVotruba\Website\TweetPublisher\TwitterApi\TwitterApiWrapper;

/**
 * @inspire https://gist.github.com/petrvacha/28ec8f5eac39283f1e7dce350f5a65ad
 * @thanks Petr Vacha
 */
final class TweetPublisher
{
    /**
     * @var int
     */
    private $minimalGapInDays;

    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    public function __construct(
        int $minimalGapInDays,
        PostTweetsProvider $postTweetsProvider,
        TwitterApiWrapper $twitterApiWrapper
    ) {
        $this->minimalGapInDays = $minimalGapInDays;
        $this->postTweetsProvider = $postTweetsProvider;
        $this->twitterApiWrapper = $twitterApiWrapper;
        $this->symfonyStyle = SymfonyStyleFactory::create();
    }

    public function run(): void
    {
        $daysSinceLastTweet = $this->twitterApiWrapper->getDaysSinceLastTweet();
        if ($daysSinceLastTweet < $this->minimalGapInDays) {
            $this->symfonyStyle->warning(sprintf(
                'It is only %d days since last tweet. Minimal gap is %d days, so no tweet until then.',
                $daysSinceLastTweet,
                $this->minimalGapInDays
            ));
            return;
        }

        $allPostTweets = $this->postTweetsProvider->provide();
        $allPublishedTweets = $this->twitterApiWrapper->getPublishedTweets();

        $tweetsToPublish = $this->excludeAlreadyPublishedTweets($allPostTweets, $allPublishedTweets);
        if (! count($tweetsToPublish)) {
            $this->symfonyStyle->warning('There is no new tweet to publish. Add a new one to one of your post under "tweet:" option.');
            return;
        }

        $tweet = $this->pickTweetCandidate($tweetsToPublish);
        $this->twitterApiWrapper->publishTweet($tweet);

        $this->symfonyStyle->success(sprintf(
            'Tweet "%s" was succesfully published.',
            $tweet
        ));
    }

    /**
     * @param string[] $allPostTweets
     * @param string[] $allPublishedTweets
     * @return string[]
     */
    private function excludeAlreadyPublishedTweets(array $allPostTweets, array $allPublishedTweets): array
    {
        return array_diff($allPostTweets, $allPublishedTweets);
    }

    /**
     * @param string[] $tweetsToPublish
     */
    private function pickTweetCandidate(array $tweetsToPublish): string
    {
        return array_pop($tweetsToPublish); // most recent one?
    }
}
