<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Repository;

use Nette\Utils\DateTime;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileSystem;
use TomasVotruba\Tweeter\ValueObject\PublishedPostTweet;
use Webmozart\Assert\Assert;

final class PublishedTweetRepository
{
    /**
     * @var string
     */
    private const STORAGE_FILE = __DIR__ . '/../../../../data/published_tweets.yaml';

    /**
     * @var PublishedPostTweet[]
     */
    private array $publishedPostTweets = [];

    public function __construct(
        private readonly SmartFileSystem $smartFileSystem,
    ) {
        $yamlItems = Yaml::parseFile(self::STORAGE_FILE);

        $publishedPostTweets = [];
        foreach ($yamlItems as $yamlItem) {
            $publishedPostTweets[] = new PublishedPostTweet($yamlItem['id'], DateTime::from($yamlItem['published_at']));
        }

        $this->publishedPostTweets = $publishedPostTweets;
    }

    /**
     * @return int[]
     */
    public function fetchIds(): array
    {
        $ids = [];
        foreach ($this->publishedPostTweets as $publishedPostTweet) {
            $ids[] = $publishedPostTweet->getId();
        }

        return $ids;
    }

    /**
     * @return PublishedPostTweet[]
     */
    public function fetchAll(): array
    {
        return $this->publishedPostTweets;
    }

    public function save(PublishedPostTweet $newPublishedPostTweet): void
    {
        $this->publishedPostTweets[] = $newPublishedPostTweet;

        $publishedPostTweetsArray = [];
        foreach ($this->publishedPostTweets as $publishedPostTweets) {
            $publishedPostTweetsArray[] = $publishedPostTweets->toArray();
        }


        $yamlContent = Yaml::dump($publishedPostTweetsArray);
        $this->smartFileSystem->dumpFile(self::STORAGE_FILE, $yamlContent);
    }

    public function fetchLatest(): PublishedPostTweet
    {
        $publishedPostTweets = $this->publishedPostTweets;
        $lastPublishedPostTweet = array_shift($publishedPostTweets);

        Assert::isInstanceOf($lastPublishedPostTweet, PublishedPostTweet::class);

        return $lastPublishedPostTweet;
    }
}
