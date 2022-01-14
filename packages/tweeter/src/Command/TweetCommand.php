<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use TomasVotruba\Tweeter\Randomizer;
use TomasVotruba\Tweeter\TweetFilter\PublishedTweetsFilter;
use TomasVotruba\Tweeter\TweetProvider\PostTweetsProvider;
use TomasVotruba\Tweeter\TwitterApi\TwitterPostApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PostTweet;
use TomasVotruba\Website\ValueObject\Option;

/**
 * @api
 */
final class TweetCommand extends Command
{
    public function __construct(
        private readonly PostTweetsProvider $postTweetsProvider,
        private readonly TwitterPostApiWrapper $twitterPostApiWrapper,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly PublishedTweetsFilter $publishedTweetsFilter,
        private Randomizer $randomizer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Publish new tweet from post');

        $this->addOption(Option::DRY_RUN, null, InputOption::VALUE_NONE, 'Show what tweet is next to be tweeted');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);

        $postTweets = $this->postTweetsProvider->provide();

        $unpublishedPostTweets = $this->publishedTweetsFilter->filter($postTweets);

        // no new tweets
        if ($unpublishedPostTweets === []) {
            return $this->reportNoNewTweet();
        }

        $tweetCountMessage = sprintf('Picking from %d tweets', count($unpublishedPostTweets));
        $this->symfonyStyle->title($tweetCountMessage);

        foreach ($unpublishedPostTweets as $unpublishedPostTweet) {
            $this->symfonyStyle->writeln(' * ' . $unpublishedPostTweet->getText());
            $this->symfonyStyle->newLine();
        }

        $postTweet = $this->randomizer->resolveRandomItem($unpublishedPostTweets);

        if ($isDryRun) {
            $message = sprintf('Tweet "%s" would be published.', $postTweet->getText());
            $this->symfonyStyle->success($message);
        } else {
            $this->tweet($postTweet);

            $message = sprintf('Tweet "%s" was successfully published.', $postTweet->getText());
            $this->symfonyStyle->success($message);
        }

        return self::SUCCESS;
    }

    private function reportNoNewTweet(): int
    {
        $this->symfonyStyle->warning(
            'There is no new tweet to publish. Add a new one to one of your post under "tweet:" option.'
        );

        return self::SUCCESS;
    }

    private function tweet(PostTweet $postTweet): void
    {
        if ($postTweet->getImage() !== null) {
            $this->twitterPostApiWrapper->publishTweetWithImage($postTweet->getText(), $postTweet->getImage());
        } else {
            $this->twitterPostApiWrapper->publishTweet($postTweet->getText());
        }
    }
}
