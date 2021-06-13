<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\TwitterApi\TwitterPostApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PostTweet;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;

final class PublishedTweetsFilter
{
    /**
     * @var PublishedTweet[]
     */
    private array $publishedPostTweets = [];

    public function __construct(
        private TwitterPostApiWrapper $twitterPostApiWrapper
    ) {
    }

    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        $publishedTweets = $this->getPublishedPostTweets();

        return array_filter(
            $postTweets,
            function (PostTweet $postTweet) use ($publishedTweets): bool {
                foreach ($publishedTweets as $publishedTweet) {
                    if ($publishedTweet->getLink() === $postTweet->getLink()) {
                        return true;
                    }
                }

                return false;
            }
        );
    }

    /**
     * @return PublishedTweet[]
     */
    private function getPublishedPostTweets(): array
    {
        if ($this->publishedPostTweets !== []) {
            return $this->publishedPostTweets;
        }

        $this->publishedPostTweets = $this->twitterPostApiWrapper->getPublishedTweets();
        return $this->publishedPostTweets;
    }
}
