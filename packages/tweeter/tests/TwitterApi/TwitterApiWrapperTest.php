<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests\TwitterApi;

use TomasVotruba\Tweeter\Tests\AbstractTwitterTestCase;
use TomasVotruba\Tweeter\TwitterApi\TwitterPostApiWrapper;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class TwitterApiWrapperTest extends AbstractTwitterTestCase
{
    private TwitterPostApiWrapper $twitterPostApiWrapper;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->twitterPostApiWrapper = self::$container->get(TwitterPostApiWrapper::class);

        $this->ensureEnvVariablesAreSet();
    }

    public function testGetPublishedTweets(): void
    {
        $publishedTweets = $this->twitterPostApiWrapper->getPublishedTweets();
        $publishedTweetCount = count($publishedTweets);
        $this->assertGreaterThanOrEqual(20, $publishedTweetCount);

        $this->assertInstanceOf(PublishedTweet::class, $publishedTweets[0]);
    }
}
