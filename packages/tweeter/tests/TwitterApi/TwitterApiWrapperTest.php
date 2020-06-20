<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests\TwitterApi;

use TomasVotruba\Tweeter\Tests\AbstractTwitterTestCase;
use TomasVotruba\Tweeter\TwitterApi\TwitterApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class TwitterApiWrapperTest extends AbstractTwitterTestCase
{
    private TwitterApiWrapper $twitterApiWrapper;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->twitterApiWrapper = self::$container->get(TwitterApiWrapper::class);

        $this->ensureEnvVariablesAreSet();
    }

    public function testGetPublishedTweets(): void
    {
        $publishedTweets = $this->twitterApiWrapper->getPublishedTweets();
        $this->assertGreaterThanOrEqual(20, count($publishedTweets));

        $this->assertInstanceOf(PublishedTweet::class, $publishedTweets[0]);
    }
}
