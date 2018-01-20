<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use TomasVotruba\Website\TweetPublisher\Tweet\Tweet;
use TomasVotruba\Website\TweetPublisher\TweetProvider\UnpublishedTweetsProvider;
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
     * @var TwitterApiWrapper
     */
    private $twitterApiWrapper;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var UnpublishedTweetsProvider
     */
    private $unpublishedTweetsProvider;

    public function __construct(
        int $minimalGapInDays,
        TwitterApiWrapper $twitterApiWrapper,
        UnpublishedTweetsProvider $unpublishedTweetsProvider
    ) {
        $this->minimalGapInDays = $minimalGapInDays;
        $this->twitterApiWrapper = $twitterApiWrapper;
        $this->unpublishedTweetsProvider = $unpublishedTweetsProvider;
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

        $tweetsToPublish = $this->unpublishedTweetsProvider->provide();
        if (! count($tweetsToPublish)) {
            $this->symfonyStyle->warning(
                'There is no new tweet to publish. Add a new one to one of your post under "tweet:" option.'
            );
            return;
        }

        $tweet = array_pop($tweetsToPublish);
        $this->tweet($tweet);

        $this->symfonyStyle->success(sprintf('Tweet "%s" was successfully published.', $tweet['text']));
    }

    private function tweet(Tweet $tweet): void
    {
        if ($tweet->getImage()) {
            $this->twitterApiWrapper->publishTweetWithImage($tweet->getText(), $tweet->getImage());
        } else {
            $this->twitterApiWrapper->publishTweet($tweet->getText());
        }
    }
}
