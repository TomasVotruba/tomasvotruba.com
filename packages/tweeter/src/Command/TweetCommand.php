<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use TomasVotruba\Tweeter\Configuration\Keys;
use TomasVotruba\Tweeter\Contract\TweetsProviderInterface;
use TomasVotruba\Tweeter\TweetFilter\TweetsFilter;
use TomasVotruba\Tweeter\TwitterApi\TwitterApiWrapper;
use TomasVotruba\Tweeter\ValueObject\Tweet;

final class TweetCommand extends Command
{
    private int $twitterMinimalGapInDays;

    private TwitterApiWrapper $twitterApiWrapper;

    private SymfonyStyle $symfonyStyle;

    private TweetsProviderInterface $tweetsProvider;

    private TweetsFilter $tweetsFilter;

    public function __construct(
        int $twitterMinimalGapInDays,
        TwitterApiWrapper $twitterApiWrapper,
        TweetsProviderInterface $tweetsProvider,
        TweetsFilter $tweetsFilter,
        SymfonyStyle $symfonyStyle
    ) {
        $this->twitterMinimalGapInDays = $twitterMinimalGapInDays;
        $this->twitterApiWrapper = $twitterApiWrapper;
        $this->tweetsProvider = $tweetsProvider;
        $this->tweetsFilter = $tweetsFilter;
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(sprintf('Publish new tweet from post "%s:" config', Keys::TWEET));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = sprintf('There is %d days since last tweet', $this->twitterApiWrapper->getDaysSinceLastTweet());

        $this->symfonyStyle->note($message);

        // to soon to tweet after recent tweet
        if (! $this->isNewTweetAllowed()) {
            return $this->reportTooSoonToTweet();
        }

        $postTweets = $this->tweetsProvider->provide();
        $postTweets = $this->tweetsFilter->filter($postTweets);

        // no tweetable tweet
        if (count($postTweets) === 0) {
            return $this->reportNoNewTweet();
        }

        $this->symfonyStyle->title(sprintf('Picking from %d tweets', count($postTweets)));
        foreach ($postTweets as $postTweet) {
            $this->symfonyStyle->writeln(' * ' . $postTweet->getText());
            $this->symfonyStyle->newLine(2);
        }
        $this->symfonyStyle->newLine();

        /** @var Tweet $tweet */
        $tweet = array_shift($postTweets);
        $this->tweet($tweet);

        $this->symfonyStyle->success(sprintf('Tweet "%s" was successfully published.', $tweet->getText()));

        return ShellCode::SUCCESS;
    }

    private function isNewTweetAllowed(): bool
    {
        $daysSinceLastTweet = $this->twitterApiWrapper->getDaysSinceLastTweet();

        return $daysSinceLastTweet >= $this->twitterMinimalGapInDays;
    }

    private function reportTooSoonToTweet(): int
    {
        $daysSinceLastTweet = $this->twitterApiWrapper->getDaysSinceLastTweet();

        $this->symfonyStyle->warning(sprintf(
            'Only %d days passed since last tweet. Minimal gap is %d days, so no tweet until then.',
            $daysSinceLastTweet,
            $this->twitterMinimalGapInDays
        ));

        return ShellCode::SUCCESS;
    }

    private function reportNoNewTweet(): int
    {
        $this->symfonyStyle->warning(sprintf(
            'There is no new tweet to publish. Add a new one to one of your post under "%s:" option.',
            Keys::TWEET
        ));

        return ShellCode::SUCCESS;
    }

    private function tweet(Tweet $postTweet): void
    {
        if ($postTweet->getImage() !== null) {
            $this->twitterApiWrapper->publishTweetWithImage($postTweet->getText(), $postTweet->getImage());
        } else {
            $this->twitterApiWrapper->publishTweet($postTweet->getText());
        }
    }
}
