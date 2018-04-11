<?php declare(strict_types=1);

namespace TomasVotruba\StatieTweetPublisher\Application;

use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\StatieTweetPublisher\Tweet\Tweet;
use TomasVotruba\StatieTweetPublisher\TweetProvider\PostTweetsProvider;
use TomasVotruba\StatieTweetPublisher\TweetProvider\UnpublishedTweetsResolver;
use TomasVotruba\StatieTweetPublisher\TwitterApi\TwitterApiWrapper;

/**
 * @inspire https://gist.github.com/petrvacha/28ec8f5eac39283f1e7dce350f5a65ad
 * @thanks Petr Vacha
 */
final class TweetPublisherApplication
{
    /**
     * @var int
     */
    private $minimalGapInDays;

    /**
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var UnpublishedTweetsResolver
     */
    private $unpublishedTweetsResolver;

    /**
     * @var PostTweetsProvider
     */
    private $postTweetsProvider;

    public function __construct(
        int $minimalGapInDays,
        TwitterApiWrapper $twitterApiWrapper,
        PostTweetsProvider $postTweetsProvider,
        UnpublishedTweetsResolver $unpublishedTweetsResolver,
        SymfonyStyle $symfonyStyle
    ) {
        $this->minimalGapInDays = $minimalGapInDays;
        $this->twitterApiWrapper = $twitterApiWrapper;
        $this->postTweetsProvider = $postTweetsProvider;
        $this->unpublishedTweetsResolver = $unpublishedTweetsResolver;
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run(): void
    {
        if (! $this->isRunAllowed()) {
            return;
        }

        $tweetsToPublish = $this->unpublishedTweetsResolver->excludePublishedTweets(
            $this->postTweetsProvider->provide(),
            $this->twitterApiWrapper->getPublishedTweets()
        );

        if (! count($tweetsToPublish)) {
            $this->symfonyStyle->warning(
                'There is no new tweet to publish. Add a new one to one of your post under "tweet:" option.'
            );
            return;
        }

        $tweet = $this->getTweetToPublish($tweetsToPublish);

        $this->tweet($tweet);

        $this->symfonyStyle->success(sprintf('Tweet "%s" was successfully published.', $tweet->getText()));
    }

    private function isRunAllowed(): bool
    {
        $daysSinceLastTweet = $this->twitterApiWrapper->getDaysSinceLastTweet();
        if ($daysSinceLastTweet >= $this->minimalGapInDays) {
            return true;
        }

        $this->symfonyStyle->warning(sprintf(
            'Only %d days passed since last tweet. Minimal gap is %d days, so no tweet until then.',
            $daysSinceLastTweet,
            $this->minimalGapInDays
        ));

        return false;
    }

    private function tweet(Tweet $tweet): void
    {
        if ($tweet->getImage()) {
            $this->twitterApiWrapper->publishTweetWithImage($tweet->getText(), $tweet->getImage());
        } else {
            $this->twitterApiWrapper->publishTweet($tweet->getText());
        }
    }

    /**
     * 2nd tweet and not 1st is required (if possible), because travis tweets before images is deployed,
     * so it fails. Thus the 2nd most recent tweet is used, where image is already uploaded.
     *
     * @param Tweet[] $tweetsToPublish
     */
    private function getTweetToPublish(array $tweetsToPublish): Tweet
    {
        if (count($tweetsToPublish) >= 2) {
            array_shift($tweetsToPublish);
        }

        return array_shift($tweetsToPublish);
    }
}
