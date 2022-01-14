<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetFilter;

use TomasVotruba\Tweeter\Repository\PublishedTweetRepository;
use TomasVotruba\Tweeter\ValueObject\PostTweet;

final class PublishedTweetsFilter
{
    public function __construct(
        private readonly PublishedTweetRepository $publishedTweetRepository
    ) {
    }

    /**
     * @param PostTweet[] $postTweets
     * @return PostTweet[]
     */
    public function filter(array $postTweets): array
    {
        $publishedPostIds = $this->publishedTweetRepository->fetchIds();

        return array_filter(
            $postTweets,
            fn (PostTweet $postTweet) => ! in_array($postTweet->getId(), $publishedPostIds, true)
        );
    }
}
