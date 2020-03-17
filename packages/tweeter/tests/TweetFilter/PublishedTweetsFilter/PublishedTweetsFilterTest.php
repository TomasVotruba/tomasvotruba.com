<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests\TweetFilter\PublishedTweetsFilter;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use TomasVotruba\Tweeter\TweetFilter\PublishedTweetsFilter;
use TomasVotruba\Tweeter\TweetProvider\TweetsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class PublishedTweetsFilterTest extends AbstractKernelTestCase
{
    private PublishedTweetsFilter $publishedTweetsFilter;

    private TweetsProvider $postTweetsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->postTweetsProvider = self::$container->get(TweetsProvider::class);
        $this->publishedTweetsFilter = self::$container->get(PublishedTweetsFilter::class);
    }

    public function test(): void
    {
        if (! getenv('TWITTER_CONSUMER_KEY')) {
            $this->markTestSkipped('Run Twitter test only with access tokens.');
        }

        $postTweets = $this->postTweetsProvider->provide();
        $this->assertCount(1, $postTweets);

        $unpublishedTweets = $this->publishedTweetsFilter->filter($postTweets);

        foreach ($unpublishedTweets as $unpublishedTweet) {
            // this tweet is already published, so it should not be here
            $this->assertStringNotContainsString(
                'New post on my blog: Clean and Decoupled Controllers, Commands and Event Subscribers ',
                $unpublishedTweet->getText()
            );
        }

        $this->assertCount(1, $unpublishedTweets);
    }
}
