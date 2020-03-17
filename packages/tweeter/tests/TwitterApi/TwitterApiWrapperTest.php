<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests\TwitterApi;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use TomasVotruba\Tweeter\TwitterApi\TwitterApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class TwitterApiWrapperTest extends AbstractKernelTestCase
{
    private TwitterApiWrapper $twitterApiWrapper;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->twitterApiWrapper = self::$container->get(TwitterApiWrapper::class);
    }

    public function testGetPublishedTweets(): void
    {
        if (! getenv('TWITTER_CONSUMER_KEY')) {
            $this->markTestSkipped('Run Twitter test only with access tokens.');
        }

        $publishedTweets = $this->twitterApiWrapper->getPublishedTweets();
        $this->assertGreaterThanOrEqual(20, count($publishedTweets));

        $this->assertInstanceOf(PublishedTweet::class, $publishedTweets[0]);
    }
}
