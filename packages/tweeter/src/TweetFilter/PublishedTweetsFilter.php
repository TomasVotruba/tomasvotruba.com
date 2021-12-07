<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\TweetProvider\PublishedPostTweetsProvider;
use TomasVotruba\Tweeter\ValueObject\PostTweet;

final class PublishedTweetsFilter
{
    public function __construct(
        private readonly PublishedPostTweetsProvider $publishedPostTweetsProvider
    ) {
    }

    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        $publishedPostTweets = $this->publishedPostTweetsProvider->provide();

        $unpublishedPostTweets = [];

        foreach ($postTweets as $postTweet) {
            foreach ($publishedPostTweets as $publishedPostTweet) {
                if ($postTweet->getLink() === $publishedPostTweet->getLink()) {
                    // already published
                    continue 2;
                }
            }

            $unpublishedPostTweets[] = $postTweet;
        }

        return $unpublishedPostTweets;
    }
}
