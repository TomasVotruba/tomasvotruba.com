<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Repository;

use Symfony\Component\Yaml\Yaml;

final class PublishedTweetRepository
{
    /**
     * @var int[]
     */
    private array $publishedTweetIds = [];

    public function __construct()
    {
        $this->publishedTweetIds = Yaml::parseFile(__DIR__ . '/../../../../data/published_tweet_ids.yaml');
    }

    /**
     * @return int[]
     */
    public function provideIds(): array
    {
        return $this->publishedTweetIds;
    }
}
