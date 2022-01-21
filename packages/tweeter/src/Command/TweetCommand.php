<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Command;

use Nette\Utils\DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Tweeter\Output\TweetReporter;
use TomasVotruba\Tweeter\Repository\PublishedTweetRepository;
use TomasVotruba\Tweeter\TweetFilter\PublishedTweetsFilter;
use TomasVotruba\Tweeter\TweetProvider\PostTweetsProvider;
use TomasVotruba\Tweeter\TwitterApi\TwitterPostApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PostTweet;
use TomasVotruba\Tweeter\ValueObject\PublishedPostTweet;
use TomasVotruba\Website\ValueObject\Option;

/**
 * @api
 */
final class TweetCommand extends Command
{
    private readonly int $twitterMinimalGapInDays;

    public function __construct(
        private readonly PostTweetsProvider $postTweetsProvider,
        private readonly TwitterPostApiWrapper $twitterPostApiWrapper,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly PublishedTweetsFilter $publishedTweetsFilter,
        private readonly PublishedTweetRepository $publishedTweetRepository,
        private readonly TweetReporter $tweetReporter,
        ParameterProvider $parameterProvider,
    ) {
        parent::__construct();
        $this->twitterMinimalGapInDays = $parameterProvider->provideIntParameter(Option::TWITTER_MINIMAL_GAP_IN_DAYS);
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

        if (! $isDryRun) {
            $lastPublishedPostTweet = $this->publishedTweetRepository->fetchLatest();

            $lastPublishedPostPublishedAt = $lastPublishedPostTweet->getPublishedAt();
            $diff = $lastPublishedPostPublishedAt->diff(DateTime::from('now'));

            $daysSinceLastTweet = (int) $diff->format('%a');
            if ($daysSinceLastTweet < $this->twitterMinimalGapInDays) {
                return $this->tweetReporter->reportTooSoon($daysSinceLastTweet, $this->twitterMinimalGapInDays);
            }
        }

        $postTweets = $this->postTweetsProvider->provide();

        $unpublishedPostTweets = $this->publishedTweetsFilter->filter($postTweets);

        // no new tweets
        if ($unpublishedPostTweets === []) {
            return $this->tweetReporter->reportNoNewTweet();
        }

        // pick the oldest post, as there can be chronological order
        $unpublishedPostTweet = array_pop($unpublishedPostTweets);

        if ($isDryRun) {
            $message = sprintf('Tweet "%s" would be published.', $unpublishedPostTweet->getText());
            $this->symfonyStyle->success($message);
        } else {
            $this->tweet($unpublishedPostTweet);

            $publishedPostTweet = new PublishedPostTweet($unpublishedPostTweet->getId(), DateTime::from('now'));

            $this->publishedTweetRepository->save($publishedPostTweet);

            $message = sprintf('Tweet "%s" was successfully published.', $unpublishedPostTweet->getText());
            $this->symfonyStyle->success($message);
        }

        $this->tweetReporter->reportNextUnpublishedTweets($unpublishedPostTweets);

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
