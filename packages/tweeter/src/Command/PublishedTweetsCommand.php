<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use TomasVotruba\Tweeter\TweetProvider\PublishedPostTweetsProvider;

final class PublishedTweetsCommand extends Command
{
    public function __construct(
        private PublishedPostTweetsProvider $publishedPostTweetsProvider,
        private SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Show published post tweets');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $publishedPostTweets = $this->publishedPostTweetsProvider->provide();

        foreach ($publishedPostTweets as $publishedPostTweet) {
            $this->symfonyStyle->writeln($publishedPostTweet->getText());
            $this->symfonyStyle->note($publishedPostTweet->getCreatedAt()->format('Y-m-d H:i'));
            $this->symfonyStyle->newLine(2);
        }

        return self::SUCCESS;
    }
}
