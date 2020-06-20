<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\Tests;

use TomasVotruba\Tweeter\TweetProvider\TweetsProvider;
use TomasVotruba\Website\HttpKernel\TomasVotrubaKernel;

final class TweetsProviderTest extends AbstractTwitterTestCase
{
    private TweetsProvider $postTweetsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(TomasVotrubaKernel::class);

        $this->postTweetsProvider = self::$container->get(TweetsProvider::class);

        $this->ensureEnvVariablesAreSet();
    }

    public function test(): void
    {
        $postTweets = $this->postTweetsProvider->provide();
        $this->assertGreaterThan(200, $postTweets);

        $lastKey = array_key_last($postTweets);
        $oldestPost = $postTweets[$lastKey];

        $postDate = $oldestPost->getPostDateTime()->format('Y-m-d');
        $this->assertSame('2016-09-09', $postDate);
    }
}
