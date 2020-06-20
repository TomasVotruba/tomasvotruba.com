<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests\TweetFilter\PublishedTweetsFilter;

use TomasVotruba\Tweeter\Tests\AbstractTwitterTestCase;
use TomasVotruba\Tweeter\TweetFilter\PublishedTweetsFilter;
use TomasVotruba\Tweeter\TweetProvider\TweetsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class PublishedTweetsFilterTest extends AbstractTwitterTestCase
{
    private PublishedTweetsFilter $publishedTweetsFilter;

    private TweetsProvider $tweetsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->tweetsProvider = self::$container->get(TweetsProvider::class);
        $this->publishedTweetsFilter = self::$container->get(PublishedTweetsFilter::class);

        $this->ensureEnvVariablesAreSet();
    }

    public function test(): void
    {
        $postTweets = $this->tweetsProvider->provide();
        $this->assertGreaterThan(200, $postTweets);

        $unpublishedTweets = $this->publishedTweetsFilter->filter($postTweets);

        foreach ($unpublishedTweets as $unpublishedTweet) {
            // this tweet is already published, so it should not be here
            $this->assertStringNotContainsString(
                'New post on my blog: Clean and Decoupled Controllers, Commands and Event Subscribers ',
                $unpublishedTweet->getText()
            );
        }
    }
}
