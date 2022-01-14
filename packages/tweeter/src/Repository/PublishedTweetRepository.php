<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Repository;

use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileSystem;

final class PublishedTweetRepository
{
    /**
     * @var string
     */
    private const STORAGE_FILE = __DIR__ . '/../../../../data/published_tweet_ids.yaml';

    /**
     * @var int[]
     */
    private array $publishedTweetIds = [];

    public function __construct(
        private readonly SmartFileSystem $smartFileSystem,
    ) {
        $this->publishedTweetIds = Yaml::parseFile(self::STORAGE_FILE);
    }

    /**
     * @return int[]
     */
    public function provideIds(): array
    {
        return $this->publishedTweetIds;
    }

    public function saveId(int $id): void
    {
        $newPublishedTweetIds = [$id, ...$this->publishedTweetIds];

        $publishedTweetIdsYaml = Yaml::dump($newPublishedTweetIds);
        $this->smartFileSystem->dumpFile(self::STORAGE_FILE, $publishedTweetIdsYaml);
    }
}
