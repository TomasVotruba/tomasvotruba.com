<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Tweeter\Configuration\Keys;
use TomasVotruba\Tweeter\Exception\ShouldNotHappenException;
use TomasVotruba\Tweeter\TweetFilter\TweetsFilter;
use TomasVotruba\Tweeter\TweetProvider\PostTweetsProvider;
use TomasVotruba\Tweeter\TwitterApi\TwitterPostApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PostTweet;
use TomasVotruba\Website\ValueObject\Option;

final class TweetCommand extends Command
{
    private int $twitterMinimalGapInHours;

    public function __construct(
        ParameterProvider $parameterProvider,
        private PostTweetsProvider $postTweetsProvider,
        private TweetsFilter $tweetsFilter,
        private TwitterPostApiWrapper $twitterPostApiWrapper,
        private SymfonyStyle $symfonyStyle
    ) {
        $this->twitterMinimalGapInHours = $parameterProvider->provideIntParameter(
            Option::TWITTER_MINIMAL_GAP_IN_HOURS
        );

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $description = sprintf('Publish new tweet from post "%s:" config', Keys::TWEET);
        $this->setDescription($description);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hoursSinceLastTweet = $this->twitterPostApiWrapper->getHoursSinceLastTweet();
        if ($hoursSinceLastTweet < $this->twitterMinimalGapInHours) {
            return $this->reportTooSoon($hoursSinceLastTweet);
        }

        $postTweets = $this->postTweetsProvider->provide();
        $unpublishedPostTweets = $this->tweetsFilter->filter($postTweets);

        // no tweetable tweet
        if (count($unpublishedPostTweets) === 0) {
            return $this->reportNoNewTweet();
        }

        $tweetCountMessage = sprintf('Picking from %d tweets', count($unpublishedPostTweets));
        $this->symfonyStyle->title($tweetCountMessage);

        foreach ($unpublishedPostTweets as $unpublishedPostTweet) {
            $this->symfonyStyle->writeln(' * ' . $unpublishedPostTweet->getText());
            $this->symfonyStyle->newLine();
        }
        $this->symfonyStyle->newLine();

        $aboutToBePublishedTweet = $this->resolveRandomTweet($unpublishedPostTweets);
        $this->tweet($aboutToBePublishedTweet);

        $message = sprintf('Tweet "%s" was successfully published.', $aboutToBePublishedTweet->getText());
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }

    private function reportNoNewTweet(): int
    {
        $noTweetMessage = sprintf(
            'There is no new tweet to publish. Add a new one to one of your post under "%s:" option.',
            Keys::TWEET
        );
        $this->symfonyStyle->warning($noTweetMessage);

        return ShellCode::SUCCESS;
    }

    private function tweet(PostTweet $postTweet): void
    {
        if ($postTweet->getImage() !== null) {
            $this->twitterPostApiWrapper->publishTweetWithImage($postTweet->getText(), $postTweet->getImage());
        } else {
            $this->twitterPostApiWrapper->publishTweet($postTweet->getText());
        }
    }

    private function reportTooSoon(int $hoursSinceLastTweet): int
    {
        // to soon to tweet after recent tweet
        $toSoonMessage = sprintf(
            'There is %d hours since last. Gap of %d hours is required',
            $hoursSinceLastTweet,
            $this->twitterMinimalGapInHours
        );

        $this->symfonyStyle->warning($toSoonMessage);

        return ShellCode::SUCCESS;
    }

    /**
     * @param PostTweet[] $tweets
     */
    private function resolveRandomTweet(array $tweets): PostTweet
    {
        $randomKey = array_rand($tweets);

        $tweet = $tweets[$randomKey];
        if (! $tweet instanceof PostTweet) {
            throw new ShouldNotHappenException();
        }

        return $tweet;
    }
}
