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
use TomasVotruba\Tweeter\TweetFilter\TweetsFilter;
use TomasVotruba\Tweeter\TweetProvider\PostTweetsProvider;
use TomasVotruba\Tweeter\TwitterApi\TwitterPostApiWrapper;
use TomasVotruba\Tweeter\ValueObject\Tweet;
use TomasVotruba\Website\ValueObject\Option;

final class TweetCommand extends Command
{
    private int $twitterMinimalGapInDays;

    public function __construct(
        ParameterProvider $parameterProvider,
        private PostTweetsProvider $postTweetsProvider,
        private TweetsFilter $tweetsFilter,
        private TwitterPostApiWrapper $twitterPostApiWrapper,
        private SymfonyStyle $symfonyStyle
    ) {
        $this->twitterMinimalGapInDays = $parameterProvider->provideIntParameter(
            Option::TWITTER_MINIMAL_GAP_IN_DAYS
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
        $message = sprintf('There is %d days since last tweet', $this->twitterPostApiWrapper->getDaysSinceLastTweet());

        $this->symfonyStyle->note($message);

        // to soon to tweet after recent tweet
        if (! $this->isNewTweetAllowed()) {
            return $this->reportTooSoonToTweet();
        }

        $postTweets = $this->postTweetsProvider->provide();
        $postTweets = $this->tweetsFilter->filter($postTweets);

        // no tweetable tweet
        if (count($postTweets) === 0) {
            return $this->reportNoNewTweet();
        }

        $tweetCountMessage = sprintf('Picking from %d tweets', count($postTweets));
        $this->symfonyStyle->title($tweetCountMessage);

        foreach ($postTweets as $postTweet) {
            $this->symfonyStyle->writeln(' * ' . $postTweet->getText());
            $this->symfonyStyle->newLine(2);
        }
        $this->symfonyStyle->newLine();

        /** @var Tweet $tweet */
        $tweet = array_shift($postTweets);
        $this->tweet($tweet);

        $message = sprintf('Tweet "%s" was successfully published.', $tweet->getText());
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }

    private function isNewTweetAllowed(): bool
    {
        $daysSinceLastTweet = $this->twitterPostApiWrapper->getDaysSinceLastTweet();

        return $daysSinceLastTweet >= $this->twitterMinimalGapInDays;
    }

    private function reportTooSoonToTweet(): int
    {
        $daysSinceLastTweet = $this->twitterPostApiWrapper->getDaysSinceLastTweet();

        $toSoonMessage = sprintf(
            'Only %d days passed since last tweet. Minimal gap is %d days, so no tweet until then.',
            $daysSinceLastTweet,
            $this->twitterMinimalGapInDays
        );
        $this->symfonyStyle->warning($toSoonMessage);

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

    private function tweet(Tweet $postTweet): void
    {
        if ($postTweet->getImage() !== null) {
            $this->twitterPostApiWrapper->publishTweetWithImage($postTweet->getText(), $postTweet->getImage());
        } else {
            $this->twitterPostApiWrapper->publishTweet($postTweet->getText());
        }
    }
}
