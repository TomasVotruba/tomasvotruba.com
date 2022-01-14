<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Output;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Tweeter\ValueObject\PostTweet;

final class TweetReporter
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
    ) {
    }

    public function reportNoNewTweet(): int
    {
        $this->symfonyStyle->warning(
            'There is no new tweet to publish. Add a new one to one of your post under "tweet:" option.'
        );

        return Command::SUCCESS;
    }

    /**
     * @param PostTweet[] $postTweets
     */
    public function reportNextUnpublishedTweets(array $postTweets): void
    {
        if ($postTweets === []) {
            return;
        }

        $this->symfonyStyle->title('Next Tweets to be Published');

        foreach ($postTweets as $postTweet) {
            $this->symfonyStyle->writeln(' * ' . $postTweet->getText());
            $this->symfonyStyle->newLine();
        }
    }

    public function reportTooSoon(int $daysSinceLastTweet, int $requiredDaysSinceLastTweet): int
    {
        // to soon to tweet after recent tweet
        $toSoonMessage = sprintf(
            'There is %d days since last. Gap of %d days is required',
            $daysSinceLastTweet,
            $requiredDaysSinceLastTweet
        );

        $this->symfonyStyle->warning($toSoonMessage);

        return Command::SUCCESS;
    }
}
