<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests\TweetFilter\PublishedTweetsFilter;

use TomasVotruba\Tweeter\Tests\AbstractTwitterTestCase;
use TomasVotruba\Tweeter\TweetFilter\PublishedTweetsFilter;
use TomasVotruba\Tweeter\TweetProvider\PostTweetsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class PublishedTweetsFilterTest extends AbstractTwitterTestCase
{
    private PublishedTweetsFilter $publishedTweetsFilter;

    private PostTweetsProvider $postTweetsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->postTweetsProvider = self::$container->get(PostTweetsProvider::class);
        $this->publishedTweetsFilter = self::$container->get(PublishedTweetsFilter::class);

        $this->ensureEnvVariablesAreSet();
    }

    public function test(): void
    {
        $localPostTweets = $this->postTweetsProvider->provide();
        $this->assertGreaterThan(200, $localPostTweets);

        $unpublishedTweets = $this->publishedTweetsFilter->filter($localPostTweets);

        // just to be sure there is no leak
        $unpublishedTweetCount = count($unpublishedTweets);
        $this->assertLessThan(20, $unpublishedTweetCount);

        foreach ($unpublishedTweets as $unpublishedTweet) {
            // this tweet is already published, so it should not be here
            $this->assertStringNotContainsString(
                'New post on my blog: Clean and Decoupled Controllers, Commands and Event Subscribers ',
                $unpublishedTweet->getText()
            );
        }
    }
}
